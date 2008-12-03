<?php
/**
 *		Plugin Name:		FV Community News
 *		Plugin URI:			http://www.frank-verhoeven.com/wordpress-plugin-fv-community-news/
 *		Description:		Let visiters of your site post their articles on your site. Like this plugin? Please consider <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=SB62B7H867Y4C&lc=US&item_name=Frank%20Verhoeven&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted">making a small donation</a>.
 *		Version:			1.2.3
 *		Author:				Frank Verhoeven
 *		Author URI:			http://www.frank-verhoeven.com/
 *		
 *		@copyright			Copyright (c) 2008, Frank Verhoeven
 *		@package 			FV Community News
 *		@author 			Frank Verhoeven
 */

/**
 *		@var bool $fvCommunityNewsSubmited True if a submission is posted, false otherwise.
 */
$fvCommunityNewsSubmited = false;

/**
 *		@var bool $fvCommunityNewsAwaitingModeration True if a submission is awaiting moderation, false otherwise.
 */
$fvCommunityNewsAwaitingModeration = false;

/**
 *		@var bool $fvCommunityNewsSubmitError True if errors occured while posting a submission, false otherwise.
 */
$fvCommunityNewsSubmitError = false;

/**
 *		@var array $fvCommunityNewsFieldValues The default values for the submission form.
 */
$fvCommunityNewsFieldValues = array(
	'fvCommunityNewsName'=>'',
	'fvCommunityNewsEmail'=>'',
	'fvCommunityNewsTitle'=>'',
	'fvCommunityNewsLocation'=>'http://',
	'fvCommunityNewsDescription'=>''
	);

/**
 *		@var int $fvCommunityNewsVersion Current version of FV Community News.
 */
$fvCommunityNewsVersion = '1.2.3';

/**
 *		Initialize the application
 *		@version 1.2.1
 */
function fvCommunityNewsInit() {
	global $fvCommunityNewsVersion;
	
	if (!headers_sent() && !session_id())
		session_start();
	
		// Plugin directory (Pre-2.6 compatibility)
	if (!defined('WP_CONTENT_URL'))
		define('WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content');
	if (!defined('WP_PLUGIN_URL'))
		define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
	
	if (!get_option('fvcn_version'))
		fvCommunityNewsInstall();
	if (version_compare(get_option('fvcn_version'), $fvCommunityNewsVersion, '<'))
		fvCommunityNewsUpdate();
	
	add_action('wp_head', 'fvCommunityNewsHead', 30);
	add_action('admin_head', 'fvCommunityNewsAdminHead');
	add_action('admin_menu', 'fvCommunityNewsAddAdmin', 20);
	add_action('admin_menu', 'fvCommunityNewsFixAdminMenu', 60);
	add_filter('plugin_action_links', 'fvCommunityNewsAddSettingsLink', 10, 2);
	add_action('wp_dashboard_setup', 'fvCommunityNewsAddDashboard');
	
	
		// Add RSS Feed if enabled
	if (get_option('fvcn_rssEnabled')) {
		$location = (get_option('fvcn_rssLocation')?get_option('fvcn_rssLocation'):'community-news.rss');
		update_option('fvcn_rssHook', add_feed($location, 'fvCommunityNewsRSSFeed') );
	}
	
		// Add widgets
	fvCommunityNewsAddWidgets();
	
		// If our form is submitted
	if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['fvCommunityNews'])) {
		fvCommunityNewsSubmit();
		
			// Ajax Requests
		if (isset($_GET['fvCommunityNewsAjaxRequest']))
			fvCommunityNewsAjaxResponse();
	}
	
		// If a captcha image is requested
	if (isset($_GET['fvCommunityNewsCaptcha']))
		fvCommunityNewsCreateCaptcha();
	
		// Admin actions
	if (isset($_GET['fvCommunityNewsAdminAction'], $_GET['s']))
		fvCommunityNewsAdminRequest();
	
}
add_action('init', 'fvCommunityNewsInit');

/**
 *		Include the form for sbimtting news.
 *		@version 1.0
 */
function fvCommunityNewsForm() {
	if (file_exists(dirname(__FILE__) . '/fvCommunityNewsForm.php')) {
		include dirname(__FILE__) . '/fvCommunityNewsForm.php';
	} else {
		echo '<!-- Couldn\'t find the form template. //-->' . "\n";
	}
}

/**
 *		Check if captcha's are enabled.
 *		@return bool True if enabled, false otherwise.
 *		@version 1.1
 */
function fvCommunityNewsCaptcha() {
	if (get_option('fvcn_captchaEnabled') && !(get_option('fvcn_hideCaptchaLoggedIn') && is_user_logged_in()))
		return true;
	return false;
}

/**
 *		Convert a string to an array, required for PHP4 compatibility.
 *		@param string $string The input string.
 *		@param int $split_length Maximum length of the chunk.
 *		@return array The splitted string.
 *		@version 1.0
 */
if(!function_exists('str_split')) {
	function str_split($string, $split_length=1) {
		$array = explode("\r\n", chunk_split($string, $split_length));
		array_pop($array);
		return $array;
	}
}

/**
 *		Converts hexadecimal colors to their rgb value.
 *		@param string $hex The hexadecimal color.
 *		@return array The rgb values.
 *		@version 1.1
 */
function fvCommunityNewsHexToRgb($hex) {
	if (!ctype_xdigit($hex) || (6 != strlen($hex) && 3 != strlen($hex)))
		return false;
	
	if (3 == strlen($hex)) {
		$h = str_split($hex, 1);
		$hex = $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
	}
	
	foreach (str_split($hex, 2) as $h)
		$dec[] = hexdec($h);
	return $dec;
}

/**
 *		Break a string if it's longer then approved.
 *		@param string $string The input string.
 *		@param int $maxLength The maximum number of chars a string may have.
 *		@param strin $breaker A character at the and of a breaked string.
 *		@return string The shorter string.
 *		@version 1.1
 */
function fvCommunityNewsBreaker($string, $maxLength, $breaker='&hellip;') {
	$string = strlen($string)>$maxLength ? substr($string, 0, $maxLength).$breaker : $string; 
	
	if ('<p' == substr($string, 0, 2) && '</p>' != substr($string, -4))
		$string .= '</p>';
	
	return $string;
}

/**
 *		Setup a captcha image.
 *		@version 1.0
 */
function fvCommunityNewsCreateCaptcha() {
	if (isset($_SESSION['fvCommunityNewsCaptcha']))
		unset($_SESSION['fvCommunityNewsCaptcha']);
	
	$from = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$from = str_shuffle($from);
	
	$length = (int)get_option('fvcn_captchaLength');
	
	$str = '';
	for ($i=0; $i<$length; $i++) {
		$str .= substr($from, mt_rand(0, strlen($from)-1), 1);
	}
	
	$str = str_shuffle($str);
	$_SESSION['fvCommunityNewsCaptcha'] = sha1($str);
	
	if (fvCommunityNewsDisplayCaptcha($str))
		exit;
}

/**
 *		Create and display a captcha image.
 *		@param string $string The string for the captcha.
 *		@version 1.0.2
 */
function fvCommunityNewsDisplayCaptcha($string) {
	$factor = 27;
	$width = $factor * strlen($string);
	$height = 37;
	$image = imagecreatetruecolor($width, $height);
	
	$dir = dirname(__FILE__) . '/fonts/';
	$fonts = array('comic.ttf', 'verdana.ttf', 'times.ttf');
	shuffle($fonts);
	
	list($r, $g, $b) = fvCommunityNewsHexToRgb( get_option('fvcn_captchaBgColor') );
	$background_color = imagecolorallocate($image, $r, $g, $b);
	list($r, $g, $b) = fvCommunityNewsHexToRgb( get_option('fvcn_captchaLColor') );
	$line_color = imagecolorallocate($image, $r, $g, $b);
	list($r, $g, $b) = fvCommunityNewsHexToRgb( get_option('fvcn_captchaTsColor') );
	$shadow_color = imagecolorallocate($image, $r, $g, $b);
	list($r, $g, $b) = fvCommunityNewsHexToRgb( get_option('fvcn_captchaTColor') );
	$text_color = imagecolorallocate($image, $r, $g, $b);
	
	imagefill($image, 0, 0, $background_color);
	
	$size = 15;
	$x_left = 5;
	
	for ($i = 0; $i < strlen($string); $i++) {
		$angle = mt_rand(-15, 15);
		$x = mt_rand($x_left - 3, $x_left + 3);
		$y = round( mt_rand(3/6*$height, 5/6*$height) );
		$font = $dir . $fonts[ mt_rand(0, 2) ];
		$char = $string{$i};	
		
		imagettftext($image, $size, $angle, $x + mt_rand(1, 3), $y + mt_rand(-3, 3), $shadow_color, $font, $char);
		imagettftext($image, $size, $angle, $x, $y, $text_color, $font, $char);
		
		$x_left += $factor;
	}
	
	$x_end = $width;
	$number = mt_rand(3, 5);
	$spread = 0;
	
	for ($i = 0; $i<$number; $i++) {
		$y_start = mt_rand(-$spread, $height + 10);
		$y_end = mt_rand(-$spread, $height + 10);
		
		imageline($image, 0, $y_start, $x_end, $y_end, $line_color);
	}
		
	header('Content-type: image/jpeg');
	imagejpeg($image);
}

/**
 *		Checks if there is a sbumission posted and there are no errors occured.
 *		@return bool True if a submission is posted succesfull, false otherwise.
 *		@version 1.0
 */
function fvCommunityNewsSubmitted() {
	global $fvCommunityNewsSubmited, $fvCommunityNewsSubmitError;
	
	if ($fvCommunityNewsSubmited && !$fvCommunityNewsSubmitError)
		return true;
	return false;
}

/**
 *		Add some stuff to the header.
 *		@version 1.1.1
 */
function fvCommunityNewsHead() {
	global $wp_rewrite, $fvCommunityNewsVersion;
	$dir = WP_PLUGIN_URL . '/fv-community-news/javascript/';
	
	echo "\n\t\t" . '<script type="text/javascript" src="' . $dir . 'prototype.js"></script>' . "\n";
	echo "\t\t" . '<script type="text/javascript" src="' . $dir . 'scriptaculous.js"></script>' . "\n";
	echo "\t\t" . '<script type="text/javascript" src="' . $dir . 'fvCommunityNews.js"></script>' . "\n";
	if (get_option('fvcn_rssEnabled')) {
		if ($wp_rewrite->using_permalinks())
			$location = get_option('home') . '/' . str_replace('feed/%feed%', '', $wp_rewrite->get_feed_permastruct());
		else
			$location = get_option('home') . '/?feed=';
		$location .= get_option('fvcn_rssLocation');
		
		echo "\t\t" . '<link rel="alternate" type="application/rss+xml" title="' . get_option('blogname') . ' Community News RSS Feed" href="' . $location . '" />' . "\n";
	}
	echo "\t\t" . '<meta name="Community-News-Creator" content="FV Community News - ' . $fvCommunityNewsVersion . '" />' . "\n\n";
}

/**
 *		Install the application.
 *		@version 1.2
 */
function fvCommunityNewsInstall() {
	global $wpdb, $fvCommunityNewsVersion;
	
	add_option('fvcn_version', $fvCommunityNewsVersion);
	
	$tableName = $wpdb->prefix . 'fv_community_news';
	add_option('fvcn_dbname', $tableName);
	add_option('fvcn_dbversion', '1.1');
	
	if ($wpdb->get_var("SHOW TABLES LIKE '" . $tableName . "'") != $tableName) {
		$sql = "CREATE TABLE " . $tableName . " (
					Id INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					Name VARCHAR( 50 ) NOT NULL ,
					Email VARCHAR( 75 ) NOT NULL ,
					Title VARCHAR( 150 ) NOT NULL ,
					Location VARCHAR( 250 ) NOT NULL ,
					Description MEDIUMTEXT NOT NULL ,
					Image VARCHAR( 25 ) NULL,
					Date DATETIME NOT NULL ,
					Ip VARCHAR( 25 ) NOT NULL ,
					Host VARCHAR( 150 ) NOT NULL,
					Approved VARCHAR( 7 ) NOT NULL
				);";
		
		if (file_exists(ABSPATH . 'wp-admin/includes/upgrade.php'))
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}
	
	
	add_option('fvcn_captchaEnabled', '0');
	add_option('fvcn_hideCaptchaLoggedIn', '1');
	add_option('fvcn_captchaLength', '6');
	add_option('fvcn_captchaBgColor', 'ecf8fe');
	add_option('fvcn_captchaLColor', 'ecf8fe');
	add_option('fvcn_captchaTsColor', '686868');
	add_option('fvcn_captchaTColor', '0b9ac7');
	add_option('fvcn_alwaysAdmin', '0');
	add_option('fvcn_previousApproved', '1');
	add_option('fvcn_mailOnSubmission', '0');
	add_option('fvcn_mailOnModeration', '1');
	add_option('fvcn_maxTitleLength', '50');
	add_option('fvcn_titleBreaker', '&hellip;');
	add_option('fvcn_maxDescriptionLength', '200');
	add_option('fvcn_descriptionBreaker', '&hellip;');
	add_option('fvcn_numSubmissions', '');
	add_option('fvcn_submissionTemplate', '');
	add_option('fvcn_formTitle', 'Add News');
	add_option('fvcn_submissionsTitle', 'Community News');
	add_option('fvcn_rssEnabled', '1');
	add_option('fvcn_numRSSItems', '10');
	add_option('fvcn_rssLocation', 'community-news.rss');
	add_option('fvcn_loggedIn', '0');
	add_option('fvcn_uploadImage', '0');
	add_option('fvcn_maxImageW', '45');
	add_option('fvcn_maxImageH', '45');
	add_option('fvcn_mySubmissions', '0');
	add_option('fvcn_akismetEnabled', '0');
	add_option('fvcn_defaultImage', 'default');
	
	$akismetApiKey = '';
	if (get_option('wordpress_api_key'))
		$akismetApiKey = get_option('wordpress_api_key');
	add_option('fvcn_akismetApiKey', $akismetApiKey);
	
	if (fvCommunityNewsMakeDirectory(ABSPATH . 'wp-fvcn-images'))
		add_option('fvcn_uploadDir', '1');
	else
		add_option('fvcn_uploadDir', '0');
}

/**
 *		Update the application.
 *		@since 1.1
 *		@version 1.1
 */
function fvCommunityNewsUpdate() {
	global $fvCommunityNewsVersion;
	
	update_option('fvcn_version', $fvCommunityNewsVersion);
	
	// Version 1.1
	add_option('fvcn_rssEnabled', '1');
	add_option('fvcn_numRSSItems', '10');
	add_option('fvcn_rssLocation', 'community-news.rss');
	add_option('fvcn_loggedIn', '0');
	add_option('fvcn_hideCaptchaLoggedIn', '1');
	
	// Version 1.2
	add_option('fvcn_mySubmissions', '0');
	add_option('fvcn_uploadImage', '0');
	add_option('fvcn_maxImageW', '45');
	add_option('fvcn_maxImageH', '45');
	add_option('fvcn_akismetEnabled', '0');
	add_option('fvcn_defaultImage', 'default');
	
	$akismetApiKey = '';
	if (get_option('wordpress_api_key'))
		$akismetApiKey = get_option('wordpress_api_key');
	add_option('fvcn_akismetApiKey', $akismetApiKey);
	
	if (1.1 > get_option('fvcn_dbversion')) {
		$sql = "CREATE TABLE " . get_option('fvcn_dbname') . " (
					Id INT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					Name VARCHAR( 50 ) NOT NULL ,
					Email VARCHAR( 75 ) NOT NULL ,
					Title VARCHAR( 150 ) NOT NULL ,
					Location VARCHAR( 250 ) NOT NULL ,
					Description MEDIUMTEXT NOT NULL ,
					Image VARCHAR( 25 ) NULL,
					Date DATETIME NOT NULL ,
					Ip VARCHAR( 25 ) NOT NULL ,
					Host VARCHAR( 150 ) NOT NULL,
					Approved VARCHAR( 7 ) NOT NULL
				);";
		
		if (file_exists(ABSPATH . 'wp-admin/includes/upgrade.php'))
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
		
		update_option('fvcn_dbversion', '1.1');
	}
	
	if (fvCommunityNewsMakeDirectory(ABSPATH . 'wp-fvcn-images'))
		add_option('fvcn_uploadDir', '1');
	else
		add_option('fvcn_uploadDir', '0');
	
	
}

/**
 *		Create a directory.
 *		@param string $dirPath The location of the dir we want to create.
 *		@param int $chomd An octal number witch contains the chmod info.
 *		@return bool Success or failed.
 *		@version 1.0.1
 *		@since 1.2
 */
function fvCommunityNewsMakeDirectory($dirPath, $chmod=0777){
	if(!is_dir($dirPath)) {
		if (mkdir($dirPath)) {
			return chmod($dirPath, $chmod);
		} else {
		   return false;
		}
	} else {
		return chmod($dirPath, $chmod);
	}
}

/**
 *		Check if the uploaded image is valid.
 *		@param array $file The uploaded file.
 *		@return bool Valid image true, else false.
 *		@version 1.0.1
 *		@since 1.2
 */
function fvCommunityNewsCheckImageUpload($file, $ignoreSize=false) {
	global $fvCommunityNewsSubmitError;
	
	$allowedImageTypes = array(
		'jpg'=>'image/jpeg',
		'jpeg'=>'image/jpeg',
		'jpe'=>'image/jpeg',
		'png'=>'image/png',
		'gif'=>'image/gif',
		'bmp'=>'image/bmp',
		'tiff'=>'image/tiff'
		);
	
	$ext = explode('.', $file['name']);
	$ext = strtolower( $ext[ count($ext)-1 ] );
	
	$imageInfo = getimagesize($file['tmp_name']);
	
	if (!array_key_exists($ext, $allowedImageTypes) || $allowedImageTypes[ $ext ] != $imageInfo['mime'] || !is_uploaded_file($file['tmp_name']) || UPLOAD_ERR_OK != $file['error'] || filesize($file['tmp_name']) > 2048000) {
		$fvCommunityNewsSubmitError = 'The file you are trying to upload isn\'t allowed.';
		return false;
	}
	
	if ( (('0' != get_option('fvcn_maxImageW') && $imageInfo[0] > (int)get_option('fvcn_maxImageW')) || 
		 ('0' != get_option('fvcn_maxImageH') && $imageInfo[1] > (int)get_option('fvcn_maxImageH'))) && !$ignoreSize) {
		$fvCommunityNewsSubmitError = 'The image you are trying to upload is too big. (max ' . ('0'==get_option('fvcn_maxImageW')?'unlimited':get_option('fvcn_maxImageW')) . ' x ' . ('0'==get_option('fvcn_maxImageH')?'unlimited':get_option('fvcn_maxImageH')) . ')';
		return false;
	}
	
	return true;
}

/**
 *		A submission is posted and handled here.
 *		@return bool True if the submission is successfull posted, false otherwise.
 *		@version 1.2.1
 */
function fvCommunityNewsSubmit() {
	global $fvCommunityNewsSubmited, $fvCommunityNewsSubmitError, $fvCommunityNewsFieldValues, $fvCommunityNewsAwaitingModeration, $wpdb;
	
	$fvCommunityNewsSubmited = true;
	
	if (get_option('fvcn_loggedIn') && !is_user_logged_in()) {
		$fvCommunityNewsSubmitError = 'You must be logged in to add a submission.';
		return false;
	}
	
	if (isset($_SESSION['fvCommunityNewsLastPost']) && $_SESSION['fvCommunityNewsLastPost'] > current_time('timestamp')) {
		$fvCommunityNewsSubmitError = 'You can only add one submission each two minutes.';
		return false;
	}
	
	if (!empty($_POST['fvCommunityNewsPhone']) || !check_admin_referer('fvCommunityNews_addSubmission')) {
		$fvCommunityNewsSubmitError = 'Move you spammer.';
		return false;
	}
	
	if (	(empty($_POST['fvCommunityNewsName'])			|| $_POST['fvCommunityNewsName'] == $fvCommunityNewsFieldValues['fvCommunityNewsName']) ||
			(empty($_POST['fvCommunityNewsEmail'])			|| $_POST['fvCommunityNewsEmail'] == $fvCommunityNewsFieldValues['fvCommunityNewsEmail']) ||
			(empty($_POST['fvCommunityNewsTitle'])			|| $_POST['fvCommunityNewsTitle'] == $fvCommunityNewsFieldValues['fvCommunityNewsTitle']) ||
			(empty($_POST['fvCommunityNewsDescription'])	|| $_POST['fvCommunityNewsDescription'] == $fvCommunityNewsFieldValues['fvCommunityNewsDescription'])	) {
		$fvCommunityNewsSubmitError = 'You didn\'t fill in all required fields.';
		return false;
	}
	
	if (get_option('fvcn_captchaEnabled') && !(get_option('fvcn_hideCaptchaLoggedIn') && is_user_logged_in())) {
		if (empty($_POST['fvCommunityNewsCaptcha'])) {
			$fvCommunityNewsSubmitError = 'You didn\'t fill in all required fields.';
			return false;
		}
		
		if (sha1($_POST['fvCommunityNewsCaptcha']) != $_SESSION['fvCommunityNewsCaptcha']) {
			$fvCommunityNewsSubmitError = 'You didn\'t fill in a valid captcha value.';
			return false;
		}
		
		unset($_SESSION['fvCommunityNewsCaptcha']);
	}
	
	if (!is_email($_POST['fvCommunityNewsEmail'])) {
		$fvCommunityNewsSubmitError = 'Please enter a valid email address.';
		return false;
	}
	
	if (get_option('fvcn_uploadImage') && isset($_FILES['fvCommunityNewsImage'], $_POST['fvCommunityNewsImageCheck']) && !empty($_FILES['fvCommunityNewsImage']['name'])) {
		if (!fvCommunityNewsCheckImageUpload($_FILES['fvCommunityNewsImage']))
			return false;
	}
	
	
	$name			= apply_filters('pre_comment_author_name', $_POST['fvCommunityNewsName']);
	$title			= apply_filters('pre_comment_author_name', $_POST['fvCommunityNewsTitle']);
	$description	= wp_filter_kses( apply_filters('pre_comment_content', $_POST['fvCommunityNewsDescription']) );
	$ip				= apply_filters('pre_comment_user_ip', $_SERVER['REMOTE_ADDR']);
	$location		= apply_filters('pre_comment_author_url', $_POST['fvCommunityNewsLocation']);
	$email			= apply_filters('pre_comment_author_email', $_POST['fvCommunityNewsEmail']);
	
	if (get_option('fvcn_alwaysAdmin')) {
		$fvCommunityNewsAwaitingModeration = true;
		if (get_option('fvcn_mailOnModeration')) {
			$modmail = true;
		}
		$approved = '0';
	} elseif (get_option('fvcn_previousApproved') && !($wpdb->query("SELECT Id FROM " . get_option('fvcn_dbname') . " WHERE Email = '" . $wpdb->escape($email) ."' AND Approved = '1'") > 0)) {
		$fvCommunityNewsAwaitingModeration = true;
		if (get_option('fvcn_mailOnModeration')) {
			$modmail = true;
		}
		$approved = '0';
	} else {
		$approved = '1';
	}
	
	// Die in hell spambitches
	if (get_option('fvcn_akismetEnabled') && file_exists( dirname(__FILE__) . '/fvCommunityNewsAkismet.php') && get_option('fvcn_akismetApiKey')) {
		@include_once dirname(__FILE__) . '/fvCommunityNewsAkismet.php';
		
		$submission = array(
		   'author'		=> $_POST['fvCommunityNewsName'],
		   'email'		=> $_POST['fvCommunityNewsEmail'],
		   'website'	=> $_POST['fvCommunityNewsLocation'],
		   'body'		=> $_POST['fvCommunityNewsDescription']
		);
		
		$akismet = new Akismet( get_option('home'), get_option('fvcn_akismetApiKey'), $submission);
	
		if(!$akismet->errorsExist() && $akismet->isSpam()) {
			$approved = 'spam';
		}
	}
	
	if (get_option('fvcn_mailOnSubmission'))
		$postmail = true;
	
	if ($postmail && 'spam' != $approved) {
		wp_mail(
			get_option('admin_email'),
			'[' . get_option('blogname') . '] Submission: "' . $title . '"',
			'New submission.' . "\n" .
			'Author:' . $name . ' (Ip: ' . $ip . ")\n" .
			'E-mail: ' . $email . "\n" .
			'URL: ' . $location . "\n" .
			'Whois: http://ws.arin.net/cgi-bin/whois.pl?queryinput=' . $ip . "\n" .
			'Description:' . "\n" . $description . "\n\n" .
			'Moderation Page: ' . get_option('home') . '/wp-admin/admin.php?page=fv-community-news&submission_status=moderation'
			);
	} elseif ($modmail && 'spam' != $approved) {
		wp_mail(
			get_option('admin_email'),
			'[' . get_option('blogname') . '] Please Moderate: "' . $title . '"',
			'A new submission is waiting for your approval.' . "\n" .
			'Author:' . $name . ' (Ip: ' . $ip . ")\n" .
			'E-mail: ' . $email . "\n" .
			'URL: ' . $location . "\n" .
			'Whois: http://ws.arin.net/cgi-bin/whois.pl?queryinput=' . $ip . "\n" .
			'Description:' . "\n" . $description . "\n\n" .
			'Moderation Page: ' . get_option('home') . '/wp-admin/admin.php?page=fv-community-news&submission_status=moderation'
			);
	}
	
	$sql = "INSERT INTO " . get_option('fvcn_dbname') . "
			(
				Name,
				Email,
				Title,
				Location,
				Description,
				Date,
				Ip,
				Host,
				Approved
			)
			VALUES
			(
				'" . $wpdb->escape($name) . "',
				'" . $wpdb->escape($email) . "',
				'" . $wpdb->escape($title) . "',
				'" . $wpdb->escape($location) . "',
				'" . $wpdb->escape($description) . "',
				'" . $wpdb->escape( current_time('mysql', 1) ) . "',
				'" . $wpdb->escape($ip) . "',
				'" . $wpdb->escape( @gethostbyaddr($ip) ) . "',
				'" . (int)$approved . "'
			)";
	$result = $wpdb->query($sql);
	if (!$result) {
		$fvNewPosterSubmitError = 'Unable to add your post, please try again later.';
		return false;
	}
	
	if (get_option('fvcn_uploadImage') && isset($_FILES['fvCommunityNewsImage'], $_POST['fvCommunityNewsImageCheck']) && !empty($_FILES['fvCommunityNewsImage']['name'])) {
		$ext = explode('.', $_FILES['fvCommunityNewsImage']['name']);
		$ext = strtolower( $ext[ count($ext)-1 ] );
		$name = $wpdb->insert_id . '.' . $ext;
		
		move_uploaded_file($_FILES['fvCommunityNewsImage']['tmp_name'], ABSPATH . 'wp-fvcn-images/' . $name);
		
		$wpdb->query("UPDATE " . get_option('fvcn_dbname') . " SET Image = '" . $wpdb->escape($name) . "' WHERE Id = '" . $wpdb->insert_id . "'");
	}
	
	$_SESSION['fvCommunityNewsLastPost'] = (current_time('timestamp')+120);
	
	return true;
}

/**
 *		Gives the errors (if any) from the posted submission.
 *		@return bool False if no errors occured, otherwise a string containing the occured error.
 *		@version 1.0.1
 */
function fvCommunityNewsSubmitError() {
	global $fvCommunityNewsSubmitError;
	return $fvCommunityNewsSubmitError;
}

/**
 *		Check if a submission is awaiting moderation.
 *		@return bool True if a submission is awaiting moderation, false otherwise.
 *		@version 1.0
 */
function fvCommunityNewsAwaitingModeration() {
	global $fvCommunityNewsAwaitingModeration;
	return $fvCommunityNewsAwaitingModeration;
}

/**
 *		Create an ajax response.
 *		@version 1.0
 *		@since 1.1
 */
function fvCommunityNewsAjaxResponse() {
	if (!headers_sent())
		header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
	
	$response = '<fvCommunityNewsAjaxResponse>';
	
	if (fvCommunityNewsSubmitError()) {
		$response .= '<status>error</status>';
		$response .= '<message>' . fvCommunityNewsSubmitError() . '</message>';
	} elseif (fvCommunityNewsAwaitingModeration()) {
		$response .= '<status>moderation</status>';
		$response .= '<message>Your submission has been added to the moderation queue and will appear soon. Thank you!</message>';
	} else {
		$response .= '<status>approved</status>';
		$response .= '<message>Your submission has been added. Thank you!</message>';
	}
	
	$response .= '</fvCommunityNewsAjaxResponse>';
	
	die ($response);
}

/**
 *		Get the value of a form field.
 *		@return string The current value of a form field.
 *		@version 1.1
 */
function fvCommunityNewsGetValue($fieldName) {
	global $userdata, $fvCommunityNewsFieldValues;
	
	if (!array_key_exists($fieldName, $fvCommunityNewsFieldValues))
		return '';
	
	if (isset($_POST[ $fieldName ]))
		return stripslashes( strip_tags($_POST[ $fieldName ]) );
	
	if (is_user_logged_in()) {
		get_currentuserinfo();
		
		switch ($fieldName) {
			case 'fvCommunityNewsName' :
				return $userdata->display_name;
				break;
			case 'fvCommunityNewsEmail' :
				return $userdata->user_email;
				break;
			case 'fvCommunityNewsLocation' :
				return $userdata->user_url;
				break;
			default :
				return $fvCommunityNewsFieldValues[ $fieldName ];
				break;
		}
	} else {
		return $fvCommunityNewsFieldValues[ $fieldName ];
	}
}

/**
 *		Create a list of submissions.
 *		@param int $number The number of submissions to be displayed.
 *		@param string $format The format of a submission.
 *		@return string The list of submissions.
 *		@version 1.2
 */
function fvCommunityNewsGetSubmissions($number=false, $format=false) {
	global $wpdb;
	
	if (!$number) {
		if (get_option('fvcn_numSubmissions') && 0 != (int)get_option('fvcn_numSubmissions'))
			$number = get_option('fvcn_numSubmissions');
		else
			$number = 5;
	}
	
	if (!$format) {
		if (get_option('fvcn_submissionTemplate'))
			$format = stripslashes(get_option('fvcn_submissionTemplate'));
		else
			$format = '<li><h3><a href="%submission_url%" title="%submission_title%">%submission_title%</a></h3><small>%submission_date%</small><br />%submission_description%</li>';
	}
	
	$sql = "SELECT
				Name,
				Email,
				Title,
				Location,
				Description,
				Image,
				Date
			FROM
				" . get_option('fvcn_dbname') . "
			WHERE
				Approved = '1'
			ORDER BY
				Date DESC
			LIMIT
				" . (int)$wpdb->escape($number) . "";
	
	$posts = $wpdb->get_results($sql);
	
	$noImage = ('default'==get_option('fvcn_defaultImage')?WP_PLUGIN_URL.'/fv-community-news/images/default.png':get_option('home').'/wp-fvcn-images/default.'.get_option('fvcn_defaultImage'));
	
	if (empty($posts))
		return '<!-- No posts found. //--><p>There are no submissions added yet, be the first.</p>';
	
	$newsPosts = '<ul class="fvCommunityNewsList">' . "\n";
	foreach ($posts as $post) {
		if (NULL == $post->Image)
			$image = $noImage;
		else
			$image = get_option('home') . '/wp-fvcn-images/' . stripslashes(apply_filters('comment_author_url', $post->Image));
		
		$newsPosts .= $format . "\n";
		
		$newsPosts = str_replace('%submission_author%', stripslashes(apply_filters('comment_author', $post->Name)), $newsPosts);
		$newsPosts = str_replace('%submission_author_email%', stripslashes(apply_filters('comment_author_email', $post->Email)), $newsPosts);
		$newsPosts = str_replace('%submission_title%', fvCommunityNewsBreaker(stripslashes(apply_filters('comment_author', $post->Title)), get_option('fvcn_maxTitleLength'), get_option('fvcn_titleBreaker')), $newsPosts);
		$newsPosts = str_replace('%submission_url%', stripslashes(apply_filters('comment_author_url', $post->Location)), $newsPosts);
		$newsPosts = str_replace('%submission_description%', trim(fvCommunityNewsBreaker(stripslashes(apply_filters('comment_text', $post->Description)), get_option('fvcn_maxDescriptionLength'), get_option('fvcn_descriptionBreaker'))), $newsPosts);
		$newsPosts = str_replace('%submission_date%', stripslashes(apply_filters('comment_date', mysql2date(get_option('date_format'), $post->Date) )), $newsPosts);
		$newsPosts = str_replace('%submission_image%', $image, $newsPosts);
	}
	$newsPosts .= '</ul>';
	
	// Remove empty links
	$newsPosts = preg_replace('/<a href=""(.*?)>(.*?)<\/a>/', '\\2', $newsPosts);
	$newsPosts = preg_replace('/<a href="http:\/\/"(.*?)>(.*?)<\/a>/', '\\2', $newsPosts);
	
	
	return $newsPosts;
}

/**
 *		Create a RSS 2.0 feed from the latest subissions.
 *		@since 1.1
 *		@version 1.1
 */
function fvCommunityNewsRSSFeed() {
	global $wpdb;
	
	$number = get_option('fvcn_numRSSItems');
	$sql = "SELECT
				Name,
				Title,
				Location,
				Description,
				Date
			FROM
				" . get_option('fvcn_dbname') . "
			WHERE
				Approved = '1'
			ORDER BY
				Date DESC
			LIMIT
				" . (int)$wpdb->escape($number) . "";
	
	$items = $wpdb->get_results($sql);
	
	
	
	if (!headers_sent())
		header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
	
	echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/">
	
	<channel>
		<title><?php bloginfo_rss('name'); wp_title_rss(); ?> - Community News</title>
		<link><?php bloginfo_rss('url') ?></link>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
		<description><?php bloginfo_rss("description") ?></description>
		<language><?php echo get_option('rss_language'); ?></language>
		<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
		<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
		<generator>Fv Community News</generator>
		<?php
		if (empty($items)) {
			echo '<!-- No submissions found yet. //-->';
		} else {
			foreach ($items as $item) :
		?>
		
		<item>
			<title><?php echo stripslashes(apply_filters('comment_author', $item->Title)); ?></title>
			<link><?php echo stripslashes(apply_filters('comment_author_url', $item->Location)); ?></link>
			<dc:creator><?php echo stripslashes(apply_filters('comment_author', $item->Name)); ?></dc:creator>
			<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $item->Date); ?></pubDate>
			<description><![CDATA[<?php echo trim(stripslashes(apply_filters('comment_text', $item->Description))); ?>]]></description>
			<content:encoded><![CDATA[<?php echo trim(stripslashes(apply_filters('comment_text', $item->Description))); ?>]]></content:encoded>
		</item>
		<?php
			endforeach;
		}
		?>
		
	</channel>
</rss>
<?php
}

/**
 *		Add widget support.
 *		@version 1.0
 *		@since 1.2
 */
function fvCommunityNewsAddWidgets() {
	if (function_exists('register_sidebar_widget')) {
		register_sidebar_widget('Community News Form', 'fvCommunityNewsFormWidget');
		$wp_registered_widgets[sanitize_title('Community News Form')]['description'] = 'The submissions form.';
		register_widget_control('Community News Form', 'fvCommunityNewsFormWidgetControl');
		
		register_sidebar_widget('Community News Submissions', 'fvCommunityNewsGetSubmissionsWidget');
		$wp_registered_widgets[sanitize_title('Community News Submissions')]['description'] = 'A list of submissions.';
		register_widget_control('Community News Submissions', 'fvCommunityNewsGetSubmissionsWidgetControl');
		
		return true;
	}
	return false;
}

/**
 *		For people who are using widgets.
 *		@param array $args Options for the widget.
 *		@version 1.1
 */
function fvCommunityNewsFormWidget($args) {
	extract($args);
	
	echo $before_widget;
	echo $before_title . stripslashes(get_option('fvcn_formTitle')) . $after_title;
	
	echo stripslashes( apply_filters('comment_text', get_option('fvcn_formDescription')) );
	
	fvCommunityNewsForm();
	
	echo $after_widget;
}

/**
 *		Some settings for the form widget.
 *		@version 1.1
 */
function fvCommunityNewsFormWidgetControl() {
	if (!empty($_POST['fvcn_formTitle'])) {
		update_option('fvcn_formTitle', stripslashes( strip_tags($_POST['fvcn_formTitle']) ));
		update_option('fvcn_formDescription', stripslashes($_POST['fvcn_formDescription']));
	}
	
	?>
	<p>
		<label for="fvcn_formTitle">Title
			<input type="text" id="fvcn_formTitle" name="fvcn_formTitle" value="<?php echo get_option('fvcn_formTitle'); ?>" class="widefat" />
		</label>
		
		<label>Description
			<textarea name="fvcn_formDescription" id="fvcn_formDescription" rows="5" class="widefat"><?php echo get_option('fvcn_formDescription'); ?></textarea>
		</label>
	</p>
	<?php
}

/**
 *		For people who are using widgets.
 *		@param array $args Options for the widget.
 *		@version 1.1
 */
function fvCommunityNewsGetSubmissionsWidget($args) {
	extract($args);
	
	echo $before_widget;
	echo $before_title . stripslashes(get_option('fvcn_submissionsTitle')) . $after_title;
	
	echo stripslashes( apply_filters('comment_text', get_option('fvcn_submissionsDescription')) );
	
	echo fvCommunityNewsGetSubmissions();
	
	echo $after_widget;
}

/**
 *		Some settings for the submissions widget.
 *		@version 1.1
 */
function fvCommunityNewsGetSubmissionsWidgetControl() {
	if (!empty($_POST['fvcn_submissionsTitle'])) {
		update_option('fvcn_submissionsTitle', stripslashes( strip_tags($_POST['fvcn_submissionsTitle']) ));
		update_option('fvcn_submissionsDescription', stripslashes($_POST['fvcn_submissionsDescription']));
	}
	
	?>
	<p>
		<label for="fvcn_submissionsTitle">Title
		<input type="text" id="fvcn_submissionsTitle" name="fvcn_submissionsTitle" value="<?php echo get_option('fvcn_submissionsTitle'); ?>" class="widefat" />
		</label>
		
		<label>Description
			<textarea name="fvcn_submissionsDescription" id="fvcn_submissionsDescription" rows="5" class="widefat"><?php echo get_option('fvcn_submissionsDescription'); ?></textarea>
		</label>
	</p>
	<?php
}

/**
 *		Add some admin pages to the wp-admin.
 *		@version 1.1
 */
function fvCommunityNewsAddAdmin() {
	
	list($submissions, $total) = fvCommunityNewsGetSubmissionsList('moderation', 0, 1);
	
	add_menu_page('Manage Submissions', 'Submissions ' . ('0' != $total?'<span id="awaiting-mod" class="count-' . $total . '"><span class="submission-count">' . $total . '</span></span>':''), 'moderate_comments', dirname(__FILE__), 'fvCommunityNewsSubmissions');
	add_submenu_page(dirname(__FILE__), 'Community News Settings', 'Settings', 'manage_options', 'fvCommunityNewsSettings', 'fvCommunityNewsSettings');
	
	// My Submissions
	if (get_option('fvcn_mySubmissions'))
		add_submenu_page('profile.php', 'My Submissions', 'My Submissions', 'read', 'fvCommunityNewsMySubmissions', 'fvCommunityNewsMySubmissions');
}

/**
 *		Remove the submissions count from the sublevel menu.
 *		@version 1.1
 *		@since 1.2
 */
function fvCommunityNewsFixAdminMenu() {
	if (current_user_can('manage_options')) {
		global $submenu;
		
		foreach ($submenu['fv-community-news'] as $key=>$item) {
			$submenu['fv-community-news'][ $key ] = preg_replace('/Submissions <span id="awaiting-mod" class="count-\d"><span class="submission-count">\d<\/span><\/span>/', 'Submissions', $item);
		}
	}
}


/**
 *		Add a settings link to the Manage Plugins page.
 *		@param string $links The current links (without settings link).
 *		@param string $file The file.
 *		@return string The links with the settings link.
 *		@version 1.0
 *		@since 1.2
 */
function fvCommunityNewsAddSettingsLink($links, $file) {
	$plugin = plugin_basename(__FILE__);
	
	if ($file == $plugin)
		$links = array_merge(array('<a href="' . attribute_escape('admin.php?page=fvCommunityNewsSettings') . '">Settings</a>'), $links);
	
	return $links;
}

/**
 *		Add a dashboard widget to the dashboard.
 *		@version 1.0.1
 *		@since 1.2
 */
function fvCommunityNewsAddDashboard() {
	if (current_user_can('moderate_comments') && function_exists('wp_add_dashboard_widget'))
		wp_add_dashboard_widget('fvCommunityNewsDashboard', 'Submissions <a href="admin.php?page=fv-community-news" class="edit-box open-box">View All</a>', 'fvCommunityNewsDashboard');
}

/**
 *		Add current stats to the Right Now section.
 *		@version 1.1
 *		@since 1.2
 */
function fvCommunityNewsDashboard() {
	
	list($submissions, $total) = fvCommunityNewsGetSubmissionsList(false, 0, 5);
	
	if ($submissions) {
		echo ' <div id="the-submission-list" class="list:comment">';
		
		foreach ($submissions as $submission) :
			echo '<div id="submission-' . $submission->Id . '" class="even thread-even depth-1 submission-item ' . ($submission->Approved?'approved':'unapproved') . '">';
				echo get_avatar(stripslashes(apply_filters('get_submission_author_email', $submission->Email)), 50);
				echo '<h4 class="submission-meta">From <cite class="submission-author">' . stripslashes(apply_filters('get_submission_author', $submission->Name)) . '</cite> ';
				echo ' linking to <a href="' . stripslashes(apply_filters('submission_author_url', $submission->Location)) . '">' . stripslashes(apply_filters('get_submission_author', $submission->Title)) . '</a></h4>';
				echo '<blockquote><p>' . trim( stripslashes(apply_filters('submission_text', $submission->Description)) ) . '</p></blockquote>';
				echo '<p class="submission-actions">';
					
					echo '<span class="approve"><a href="';
					echo wp_nonce_url('?fvCommunityNewsAdminAction=approvesubmission&amp;s=' . $submission->Id, 'fvCommunityNews_approveSubmission' . $submission->Id);
					echo '" class="dim:the-submission-list:submission-1:unapproved:e7e7d3:e7e7d3:new=approved vim-a" title="Approve this submission">Approve</a></span>';
					
					echo '<span class="unapprove"><a href="';
					echo wp_nonce_url('?fvCommunityNewsAdminAction=unapprovesubmission&amp;s=' . $submission->Id, 'fvCommunityNews_unapproveSubmission' . $submission->Id);
					echo '" class="dim:the-submission-list:submission-1:unapproved:e7e7d3:e7e7d3:new=unapproved vim-u" title="Unapprove this submission">Unapprove</a></span>';
					
					echo '<span class="edit"> | <a href="admin.php?page=fv-community-news&amp;mode=edit-submission&amp;submission=' . $submission->Id . '" title="Edit submission">Edit</a></span>';
					
					echo '<span class="spam"> | <a href="';
					echo wp_nonce_url('?fvCommunityNewsAdminAction=spamsubmission&amp;s=' . $submission->Id, 'fvCommunityNews_spamSubmission' . $submission->Id);
					echo '" class="delete:the-submission-list:submission-1::spam=1 vim-s vim-destructive" title="Mark this submission as spam">Spam</a> | </span>';
					
					echo '<span class="delete"><a href="';
					echo wp_nonce_url('?fvCommunityNewsAdminAction=deletesubmission&amp;s=' . $submission->Id, 'fvCommunityNews_deleteSubmission' . $submission->Id);
					echo '" onclick="if ( confirm(\'' . js_escape(__("You are about to delete this submission. \n  'Cancel' to stop, 'OK' to delete.")) . '\') ) { return true;}return false;" class="delete:the-submission-list:submission-' . $submission->Id . ' delete vim-d vim-destructive">Delete</a></span>';
					
				echo '</p>';
			echo '</div>' . "\n";
		endforeach;

		
		echo '</div>' . "\n\n";
		
	} else {
		echo '<p>No submissions yet.</p>';
	}
	
}

/**
 *		Process a request called from the admin panel.
 *		@version 1.0
 *		@since 1.2
 */
function fvCommunityNewsAdminRequest() {
	global $wpdb;
	
	if (!is_user_logged_in() || !current_user_can('moderate_comments')) return false;
	
	switch ($_GET['fvCommunityNewsAdminAction']) {
		case 'approvesubmission' :
			if (!check_admin_referer('fvCommunityNews_approveSubmission' . $_GET['s'])) break;
			$wpdb->query("UPDATE " . get_option('fvcn_dbname') . " SET Approved = '1' WHERE Id = '" . $wpdb->escape($_GET['s']) . "'");
			break;
		case 'unapprovesubmission' :
			if (!check_admin_referer('fvCommunityNews_unapproveSubmission' . $_GET['s'])) break;
			$wpdb->query("UPDATE " . get_option('fvcn_dbname') . " SET Approved = '0' WHERE Id = '" . $wpdb->escape($_GET['s']) . "'");
			break;
		case 'deletesubmission' :
			if (!check_admin_referer('fvCommunityNews_deleteSubmission' . $_GET['s'])) break;
			$wpdb->query("DELETE FROM " . get_option('fvcn_dbname') . " WHERE Id = '" . $wpdb->escape($_GET['s']) . "'");
			break;
		case 'spamsubmission' :
			if (!check_admin_referer('fvCommunityNews_spamSubmission' . $_GET['s'])) break;
			$wpdb->query("UPDATE " . get_option('fvcn_dbname') . " SET Approved = 'spam' WHERE Id = '" . $wpdb->escape($_GET['s']) . "'");
			break;
		default :
			//	No action
			break;
	}
	
	if ('' != wp_get_referer())
		$redirect = wp_get_referer();
	elseif ('' != wp_get_original_referer())
		$redirect = wp_get_original_referer();
	else
		$redirect = admin_url('admin.php?page=fv-community-news');
	
		// When we're from the edit page
	if ('deletesubmission' == $_GET['fvCommunityNewsAdminAction'] && strstr($redirect, 'mode=edit-submission'))
		$redirect = admin_url('admin.php?page=fv-community-news');
	
	wp_redirect( $redirect );
}

/**
 *		Get a list of submissions for the moderation panel.
 *		@param string $status The current status of the submissions.
 *		@param int $start The start of submissions.
 *		@param int $num The number of submissions per page.
 *		@param string $extra Additional other where statements.
 *		@return array The submissions, Total amount of submissions
 *		@version 1.1
 *		@since 1.1
 */
function fvCommunityNewsGetSubmissionsList($status=false, $start, $num, $extra='') {
	global $wpdb;

	$start = abs( (int) $start );
	$num = (int) $num;

	if ('moderation' == $status)
		$approved = "Approved = '0'";
	elseif ('approved' == $status)
		$approved = "Approved = '1'";
	elseif ('spam' == $status)
		$approved = "Approved = 'spam'";
	else
		$approved = "(Approved = '0' OR Approved = '1')";

	$submissions = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM " . get_option('fvcn_dbname') . " WHERE $approved $extra ORDER BY Date DESC LIMIT " . $wpdb->escape($start) . ", " . $wpdb->escape($num));

	$total = $wpdb->get_var("SELECT FOUND_ROWS()");

	return array($submissions, $total);
}

/**
 *		Add some javascript to the admin header.
 */
function fvCommunityNewsAdminHead() {
	$dir = WP_PLUGIN_URL . '/fv-community-news/';
	if (isset($_GET['page']) && (strstr($_GET['page'], 'fv-community-news') || strstr($_GET['page'], 'fvCommunityNews')) ) {
		
		echo '<script type="text/javascript" src="' . $dir . 'javascript/fvCommunityNewsAdmin.js"></script>' . "\n";
	}
	echo '<link rel="stylesheet" href="' . $dir . 'styles/fvCommunityNewsAdmin.css" />' . "\n";
}

/**
 *		Admin page for viewing `My Submissions`
 *		@version 1.0.1
 *		@since 1.2
 */
function fvCommunityNewsMySubmissions() {
	if (!current_user_can('read'))
		exit;
	
	global $userdata, $wpdb;
	
	get_currentuserinfo();
	$submissionPerPage = 10;
	
	if (isset($_GET['apage']))
		$page = abs( (int)$_GET['apage'] );
	else
		$page = 1;
	
	$start = ($page - 1) * $submissionPerPage;
	
	list($_submissions, $total) = fvCommunityNewsGetSubmissionsList(false, $start, $submissionPerPage + 5, "AND Email='" . $wpdb->escape($userdata->user_email) . "'");
	
	$submissions = array_slice($_submissions, 0, $submissionPerPage);
	$extra_comments = array_slice($_submissions, $submissionPerPage);
	
	$pageLinks = paginate_links( array(
		'base' => add_query_arg( 'apage', '%#%' ),
		'format' => '',
		'total' => ceil($total / $submissionPerPage),
		'current' => $page
	));
		
	$noImage = ('default'==get_option('fvcn_defaultImage')?WP_PLUGIN_URL.'/fv-community-news/images/default.png':get_option('home').'/wp-fvcn-images/default.'.get_option('fvcn_defaultImage'));
	
	echo '<div class="wrap"><h2>My Submissions</h2><p>The Community News you have added.';
	
	if (empty($submissions)) :
		echo '<p>No submissions here, yet.</p>';
	else : ?>
	
		<?php if ( $pageLinks ) : ?>
		<div class="tablenav">
			<div class="tablenav-pages"><?php echo $pageLinks; ?></div>
			<br class="clear" />
		</div>
		<?php endif; ?>
		
		<table class="widefat">
			<thead>
				<tr>
					<th scope="col" id="comment" class="manage-column column-comment" style="">Submission</th>
					<th scope="col" id="author" class="manage-column column-author" style="min-width: 210px;">Author</th>
					<?php if (get_option('fvcn_uploadImage'))
						echo '<th scope="col" id="author" class="manage-column column-image">Image</th>'; ?>
					<th scope="col" id="date" class="manage-column column-date" style="min-width: 120px;">Submitted</th>
				</tr>
			</thead>
			<tbody id="the-comment-list" class="list:comment">
			<?php
			foreach ($submissions as $post) {
				echo '<tr class="' . ('0' == $post->Approved?'unapproved':'') . '">' . "\n";
				echo ' <td class="comment column-comment"><strong><a href="' . stripslashes(apply_filters('comment_author_url', $post->Location)) . '">' . stripslashes(apply_filters('get_comment_author', $post->Title)) . '</a></strong><br />';
				echo trim( stripslashes(apply_filters('comment_text', $post->Description)) );
				
				echo '</td>' . "\n";
				echo ' <td class="author column-author"><strong>' . get_avatar($post->Email, 32) . ' ' . stripslashes(apply_filters('get_comment_author', $post->Name)) . '</strong><br />';
				echo '<a href="mailto:' . stripslashes(apply_filters('get_comment_author_email', $post->Email)) . '">' . stripslashes(apply_filters('get_comment_author_email', $post->Email)) . '</a><br />';
				
				if (get_option('fvcn_uploadImage'))
					echo ' <td class="image column-image"><img src="' . (NULL==$post->Image?$noImage:get_option('home').'/wp-fvcn-images/'.$post->Image) . '" alt="" /></td>' . "\n";
				
				echo ' <td class="date column-date">' . stripslashes(apply_filters('get_comment_date', mysql2date(get_option('date_format'), $post->Date))) . '</td>' . "\n";
				echo '</tr>' . "\n\n";
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th scope="col" class="manage-column column-comment" style="">Submission</th>
					<th scope="col" class="manage-column column-author" style="">Author</th>
					<?php if (get_option('fvcn_uploadImage'))
						echo '<th scope="col" id="author" class="manage-column column-image">Image</th>'; ?>
					<th scope="col" class="manage-column column-date" style="">Submitted</th>
				</tr>
			</tfoot>
		</table>
	<?php endif; ?>
		
	<?php if ( $pageLinks ) : ?>
	<div class="tablenav">
		<div class="tablenav-pages"><?php echo $pageLinks; ?></div>
		<br class="clear" />
	</div>
	<?php endif; ?>
	<br />
	
	<h2>Add News</h2>
	<p>Add a new submission.</p>
	<?php fvCommunityNewsForm();
		
	echo '</div>';
}

/**
 *		Admin page for managing submissions.
 *		@version 1.2.1
 */
function fvCommunityNewsSubmissions() {
	global $wpdb;
	
	if (!current_user_can('moderate_comments'))
		exit;
	
	if (!isset($_GET['mode'], $_GET['submission']) || $_GET['mode'] != 'edit-submission') {
		if (empty($_GET['submission_status']))
			$submissionStatus = 'all';
		else
			$submissionStatus = attribute_escape($_GET['submission_status']);
		
		// Form submissions
		if (!empty($_POST['submissions']) && check_admin_referer('fvCommunityNews_moderateSubmissions')) {
			if (!is_array($_POST['submissions']))
				$_POST['submissions'] = array( $_POST['submissions'] );
				
				foreach ($_POST['submissions'] as $submission) {
					if (isset($_POST['submission-approve']))
						$wpdb->query("UPDATE " . get_option('fvcn_dbname') . " SET Approved = '1' WHERE Id = '" . $wpdb->escape($submission) . "'");
					if (isset($_POST['submission-unapprove']))
						$wpdb->query("UPDATE " . get_option('fvcn_dbname') . " SET Approved = '0' WHERE Id = '" . $wpdb->escape($submission) . "'");
					if (isset($_POST['submission-spam']))
						$wpdb->query("UPDATE " . get_option('fvcn_dbname') . " SET Approved = 'spam' WHERE Id = '" . $wpdb->escape($submission) . "'");
					if (isset($_POST['submission-delete']))
						$wpdb->query("DELETE FROM " . get_option('fvcn_dbname') . " WHERE Id = '" . $wpdb->escape($submission) . "'");
				}
			
			echo '<div id="moderated" class="updated fade"><p>' . count($_POST['submissions']) . ' submissions ';
			if (isset($_POST['submission-approve']) && !(isset($_POST['submission-unapprove']) || isset($_POST['submission-delete'])) )
				echo 'approved';
			if (isset($_POST['submission-unapprove']))
				echo 'unapproved';
			if (isset($_POST['submission-delete']))
				echo 'deleted';
			echo '<br /></p></div>' . "\n";
		}
		
		$wpdb->query("SELECT Id FROM " . get_option('fvcn_dbname') . " WHERE Approved = '0'");
		$numAwaitingMod = $wpdb->num_rows;
		$wpdb->query("SELECT Id FROM " . get_option('fvcn_dbname') . " WHERE Approved = 'spam'");
		$numSpam = $wpdb->num_rows;
	} else {
		if ('POST' == $_SERVER['REQUEST_METHOD'] && check_admin_referer('updateSubmission_' . $_POST['submissionId'])) {
			$wpdb->query("UPDATE " . get_option('fvcn_dbname') . "
					SET
						Name = '" . $wpdb->escape($_POST['Name']) . "',
						Email = '" . $wpdb->escape($_POST['Email']) . "',
						Title = '" . $wpdb->escape($_POST['Title']) . "',
						Location = '" . $wpdb->escape($_POST['Location']) . "',
						Description = '" . $wpdb->escape($_POST['content']) . "',
						Approved = '" . $wpdb->escape($_POST['Approved']) . "'
					WHERE
						Id = '" . $wpdb->escape($_POST['submissionId']) . "'
					");
			
			echo '<div id="moderated" class="updated fade"><p>Submission Updated.</p></div>';
			
		}
	}
	
	if ('0' == get_option('fvcn_uploadDir') && '1' == get_option('fvcn_uploadImage'))
			echo '<div class="error"><ul><li><strong>ERROR: </strong>Failed to create image dir, please create it manualy.</li></ul></div>';
	?>
<div class="wrap">
		<?php if (!isset($_GET['mode'], $_GET['submission']) || $_GET['mode'] != 'edit-submission') : ?>
		
		<h2>Manage Submissions</h2>
		<ul class="subsubsub">
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'all', $_SERVER['REQUEST_URI'])) ?>" <?php if ('all' == $submissionStatus) echo 'class="current"' ?>>Show All</a> |</li>
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'moderation', $_SERVER['REQUEST_URI'])) ?>" <?php if ('moderation' == $submissionStatus) echo 'class="current"' ?>>Awaiting Moderation <?php echo '(' . $numAwaitingMod . ')'; ?></a> |</li>
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'approved', $_SERVER['REQUEST_URI'])) ?>" <?php if ('approved' == $submissionStatus) echo 'class="current"' ?>>Approved</a> |</li>
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'spam', $_SERVER['REQUEST_URI'])) ?>" <?php if ('spam' == $submissionStatus) echo 'class="current"' ?>>Spam <?php echo '(' . $numSpam . ')'; ?></a></li>
		</ul>
		
		
		<?php
		if ('spam' == $submissionStatus)
			$submissionPerPage = 20;	// Delete spam faster
		else
			$submissionPerPage = 10;
		
		if (isset($_GET['apage']))
			$page = abs( (int)$_GET['apage'] );
		else
			$page = 1;
		
		$start = ($page - 1) * $submissionPerPage;
		
		list($_submissions, $total) = fvCommunityNewsGetSubmissionsList( $submissionStatus, $start, $submissionPerPage + 5 );
		
		$submissions = array_slice($_submissions, 0, $submissionPerPage);
		$extra_comments = array_slice($_submissions, $submissionPerPage);
		
		$pageLinks = paginate_links( array(
			'base' => add_query_arg( 'apage', '%#%' ),
			'format' => '',
			'total' => ceil($total / $submissionPerPage),
			'current' => $page
		));
		
		$noImage = ('default'==get_option('fvcn_defaultImage')?WP_PLUGIN_URL.'/fv-community-news/images/default.png':get_option('home').'/wp-fvcn-images/default.'.get_option('fvcn_defaultImage'));
		
		if (empty($submissions)) :
			if ('spam' == $submissionStatus)
				echo '<br class="clear" /><p>No submissions here, must be your lucky day.</p>';
			else
				echo '<br class="clear" /><p>No submissions here, yet.</p>';
		else : ?>
		
		<form id="comments-form" action="" method="post">
			<div class="tablenav">
				<?php
				if ( $pageLinks )
					echo '<div class="tablenav-pages">' . $pageLinks . '</div>';
				?>
				<div class="alignleft">
					<input type="submit" name="submission-approve" id="submission-approve" value="Approve" class="button-secondary" />
					<input type="submit" name="submission-unapprove" id="submission-unapprove" value="Unapprove" class="button-secondary" />
					<input type="submit" name="submission-spam" id="submission-spam" value="Spam" class="button-secondary" />
					<input type="submit" name="submission-delete" id="submission-delete" value="Delete" class="button-secondary delete" />
				</div>
				<br class="clear" />
			</div>
			<br class="clear" />
			
			<table class="widefat">
			<thead>
				<tr>
					<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" onclick="fvCommunityNewsCheckAll();" /></th>
					<th scope="col" id="comment" class="manage-column column-comment" style="">Submission</th>
					<th scope="col" id="author" class="manage-column column-author" style="min-width: 210px;">Author</th>
					<?php if (get_option('fvcn_uploadImage'))
						echo '<th scope="col" id="author" class="manage-column column-image">Image</th>'; ?>
					<th scope="col" id="date" class="manage-column column-date" style="min-width: 120px;">Submitted</th>
				</tr>
			</thead>
			<tbody id="the-comment-list" class="list:comment">
			<?php
			foreach ($submissions as $post) {
				echo '<tr id="submission-' . $post->Id . '" class="' . (('0' == $post->Approved || 'spam' == $post->Approved)?'unapproved':'') . '">' . "\n";
				echo ' <th scope="row" class="check-column"><input type="checkbox" name="submissions[]" value=' . $post->Id . ' /></th>' . "\n";
				echo ' <td class="comment column-comment"><strong><a href="' . stripslashes(apply_filters('comment_author_url', $post->Location)) . '">' . stripslashes(apply_filters('get_comment_author', $post->Title)) . '</a></strong><br />';
				echo trim( stripslashes(apply_filters('comment_text', $post->Description)) );
				
				echo '<span class="approve"><a href="';
				echo wp_nonce_url('?fvCommunityNewsAdminAction=approvesubmission&amp;s=' . $post->Id, 'fvCommunityNews_approveSubmission' . $post->Id);
				echo '" class="dim:the-submission-list:submission-1:unapproved:e7e7d3:e7e7d3:new=approved vim-a" title="Approve this submission">Approve</a></span>';

				echo '<span class="unapprove"><a href="';
				echo wp_nonce_url('?fvCommunityNewsAdminAction=unapprovesubmission&amp;s=' . $post->Id, 'fvCommunityNews_unapproveSubmission' . $post->Id);
				echo '" class="dim:the-submission-list:submission-1:unapproved:e7e7d3:e7e7d3:new=unapproved vim-u" title="Unapprove this submission">Unapprove</a></span>';
				
				echo '<span class="edit"> | <a href="admin.php?page=fv-community-news&amp;mode=edit-submission&amp;submission=' . $post->Id . '" title="Edit submission">Edit</a></span>';
				
				if ('spam' != $post->Approved) {
					echo '<span class="spam"> | <a href="';
					echo wp_nonce_url('?fvCommunityNewsAdminAction=spamsubmission&amp;s=' . $post->Id, 'fvCommunityNews_spamSubmission' . $post->Id);
					echo '" class="delete:the-submission-list:submission-1::spam=1 vim-s vim-destructive" title="Mark this submission as spam">Spam</a></span>';
				}
				
				echo '<span class="delete"> | <a href="';
				echo wp_nonce_url('?fvCommunityNewsAdminAction=deletesubmission&amp;s=' . $post->Id, 'fvCommunityNews_deleteSubmission' . $post->Id);
				echo '"onclick="if ( confirm(\'' . js_escape(__("You are about to delete this submission. \n  'Cancel' to stop, 'OK' to delete.")) . '\') ) { return true;}return false;" class="delete:the-submission-list:submission-' . $post->Id . ' delete vim-d vim-destructive">Delete</a></span>';
				
				echo '</td>' . "\n";
				echo ' <td class="author column-author"><strong>' . get_avatar($post->Email, 32) . ' ' . stripslashes(apply_filters('get_comment_author', $post->Name)) . '</strong><br />';
				echo '<a href="mailto:' . stripslashes(apply_filters('get_comment_author_email', $post->Email)) . '">' . stripslashes(apply_filters('get_comment_author_email', $post->Email)) . '</a><br />';
				echo '<a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=' . $post->Ip . '">' . $post->Ip . '</a></td>' . "\n";
				
				if (get_option('fvcn_uploadImage'))
					echo ' <td class="image column-image"><img src="' . (NULL==$post->Image?$noImage:get_option('home').'/wp-fvcn-images/'.$post->Image) . '" alt="" /></td>' . "\n";
				
				echo ' <td class="date column-date">' . stripslashes(apply_filters('get_comment_date', mysql2date(get_option('date_format'), $post->Date))) . '</td>' . "\n";
				echo '</tr>' . "\n\n";
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" onclick="fvCommunityNewsCheckAll();" /></th>
					<th scope="col" class="manage-column column-comment" style="">Submission</th>
					<th scope="col" class="manage-column column-author" style="">Author</th>
					<?php if (get_option('fvcn_uploadImage'))
						echo '<th scope="col" id="author" class="manage-column column-image">Image</th>'; ?>
					<th scope="col" class="manage-column column-date" style="">Submitted</th>
				</tr>
			</tfoot>
			</table>
			<?php wp_nonce_field('fvCommunityNews_moderateSubmissions'); ?>
			<input type="hidden" name="fvCommunityNewsAdmin" id="fvCommunityNewsAdmin" value="true" />
		
			<div class="tablenav">
				<?php
				if ( $pageLinks )
					echo '<div class="tablenav-pages">' . $pageLinks . '</div>';
				?>
				<div class="alignleft">
					<input type="submit" name="submission-approve" value="Approve" class="button-secondary" />
					<input type="submit" name="submission-unapprove" value="Unapprove" class="button-secondary" />
					<input type="submit" name="submission-spam" id="submission-spam" value="Spam" class="button-secondary" />
					<input type="submit" name="submission-delete" value="Delete" class="button-secondary delete" />
				</div>
				<br class="clear" />
			</div>
		</form>
		<?php endif; ?>
		
		<?php else : // Edit submission
		$submission = $wpdb->get_results("SELECT * FROM " . get_option('fvcn_dbname') . " WHERE Id = '" . $wpdb->escape($_GET['submission']) . "'");
		$submission =  $submission[0];
		?>
		<!--<h2>Edit Submission</h2>-->
		<form name="post" action="" method="post" id="post">
			<?php wp_nonce_field('updateSubmission_' . $submission->Id); ?>
			<input type="hidden" name="submissionId" id="submissionId" value="<?php echo $submission->Id; ?>" />
			<div id="poststuff" class="metabox-holder">
		<?php
		$email = stripslashes(attribute_escape( $submission->Email ));
		$url = stripslashes(attribute_escape( $submission->Location ));
		// add_meta_box('submitdiv', __('Save'), 'comment_submit_meta_box', 'comment', 'side', 'core');
		?>
				<div id="side-info-column" class="inner-sidebar submitbox">
					<div id="submitdiv" class="stuffbox" >
						<h3><span class='hndle'>Save</span></h3>
						<div class="submitbox" id="submitcomment">
							<div class="inside-submitbox">
								<div class="insidebox">
									<div id='comment-status-radio'>
										<p><strong>This submission is</strong></p>
										<label>
											<input type="radio"<?php checked( $submission->Approved, '1' ); ?> name="Approved" value="1" />
											Approved
										</label>
										<br />
										<label>
											<input type="radio"<?php checked( $submission->Approved, '0' ); ?> name="Approved" value="0" />
											Awaiting Moderation
										</label>
										<br />
										<label>
											<input type="radio"<?php checked( $submission->Approved, 'spam' ); ?> name="Approved" value="spam" />
											Spam
										</label>
									</div>
								</div>
								<div class="insidebox" id="deletebutton">
									<?php echo "<a class='submitdelete' href='" . wp_nonce_url('?fvCommunityNewsAdminAction=deletesubmission&amp;s=' . $_GET['submission'], 'fvCommunityNews_deleteSubmission' . $_GET['submission']) . "' onclick=\"if ( confirm('" . js_escape(__("You are about to delete this submission. \n  'Cancel' to stop, 'OK' to delete.")) . "') ) { return true;}return false;\">Delete Submission</a>"; ?>
								</div>
								<?php
								$stamp = __('%1$s at %2$s');
								$date = mysql2date(get_option('date_format'), $submission->Date);
								$time = mysql2date(get_option('time_format'), $submission->Date);
								?>
								<div class="insidebox curtime"><span id="timestamp"><?php printf($stamp, $date, $time); ?></span></div>
							</div>
							<p class="submit">
								<input type="submit" name="save" value="<?php _e('Save'); ?>" tabindex="6" class="button button-highlighted" />
								<a class="button preview" href="<?php echo clean_url('admin.php?page=fv-community-news'); ?>" style="padding:7px;">Back</a>
							</p>
						</div>
					</div>
				</div>
				<div id="post-body" class="has-sidebar">
					<div id="post-body-content" class="has-sidebar-content">
						<div id="titlediv" class="stuffbox">
							<h3><label for="Title">Title</label></h3>
							<div class="inside">
								<input type="text" name="Title" size="30" value="<?php echo stripslashes(attribute_escape( $submission->Title )); ?>" tabindex="1" id="Title" />
							</div>
						</div>
						<div id="postdiv" class="postarea">
							<h3>Submission</h3>
							<?php
							add_filter('user_can_richedit', create_function ('$a', 'return false;') , 50);	// Disable visual editor
							the_editor(stripslashes($submission->Description), 'content', 'newcomment_author_url', false, 4);
							add_filter('user_can_richedit', create_function ('$a', 'return true;') , 50);	// Enable visual editor
							?>
						</div>
						<div id="namediv" class="stuffbox">
							<h3><label for="Name">Name</label></h3>
							<div class="inside">
								<input type="text" name="Name" size="30" value="<?php echo stripslashes(attribute_escape( $submission->Name )); ?>" tabindex="3" id="Name" />
							</div>
						</div>
						<div id="emaildiv" class="stuffbox">
							<h3><label for="Email">E-mail</label></h3>
							<div class="inside">
								<input type="text" name="Email" size="30" value="<?php echo $email; ?>" tabindex="4" id="Email" />
								<?php if ( $email )
									echo '<p><a href="mailto:' . stripslashes(apply_filters('get_comment_author_email', $email)) . '">Send Email</a>'; ?>
							</div>
						</div>
						<div id="uridiv" class="stuffbox">
							<h3><label for="Location">URL</label></h3>
							<div class="inside">
								<input type="text" id="Location" name="Location" size="30" value="<?php echo $url; ?>" tabindex="5" />
								<?php
								if ( ! empty( $url ) && 'http://' != $url ) {
									$link = '<a href="' . $url . '" rel="external nofollow" target="_blank">Visit Site</a>';
									echo '<p>' . stripslashes(apply_filters('get_comment_author_link', $link)) . '</p>'; 
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		<script type="text/javascript">
		try{document.post.Title.focus();}catch(e){}
		</script>
		<?php endif; ?>
	</div>
	<?php
}

/**
 *		Admin page for settings.
 *		@version 1.2.2
 */
function fvCommunityNewsSettings() {
	if (!current_user_can('manage_options'))
		exit;
	
	global $wp_rewrite;
	
	// Remove image directory error
	if (@is_dir(ABSPATH . 'wp-fvcn-images/') && is_writable(ABSPATH . 'wp-fvcn-images/') && !(bool)get_option('fvcn_uploadDir'))
		update_option('fvcn_uploadDir', true);
		
	if ('POST' == $_SERVER['REQUEST_METHOD'] && check_admin_referer('fvCommunityNews_changeSettings')) {
		$error = false;
		
		remove_action( get_option('fvcn_rssHook'), 'fvCommunityNewsRSSFeed', 10, 1);
		
		if (!empty($_POST['fvcn_akismetEnabled']) && empty($_POST['fvcn_akismetApiKey'])) {
			$_POST['fvcn_akismetEnabled'] = false;
			$error[] = 'An API Key is required to use Akismet.';
		}
		
		if (isset($_FILES['fvcn_defaultImage']) && !empty($_FILES['fvcn_defaultImage']) && !empty($_FILES['fvcn_defaultImage']['name'])) {
			if (!fvCommunityNewsCheckImageUpload($_FILES['fvcn_defaultImage'], true)) {
				$error[] = 'The image you are trying to upload is invalid.';
			} else {
				$ext = explode('.', $_FILES['fvcn_defaultImage']['name']);
				$ext = strtolower( $ext[ count($ext)-1 ] );
				move_uploaded_file($_FILES['fvcn_defaultImage']['tmp_name'], ABSPATH . '/wp-fvcn-images/default.' . $ext);
				
				update_option('fvcn_defaultImage', $ext);
			}
		}
		
		$settings = array(
			'fvcn_captchaEnabled'		=> 'bool',
			'fvcn_hideCaptchaLoggedIn'	=> 'bool',
			'fvcn_alwaysAdmin'			=> 'bool',
			'fvcn_previousApproved'		=> 'bool',
			'fvcn_loggedIn'				=> 'bool',
			'fvcn_mySubmissions'		=> 'bool',
			'fvcn_mailOnSubmission'		=> 'bool',
			'fvcn_mailOnModeration'		=> 'bool',
			'fvcn_akismetEnabled'		=> 'bool',
			'fvcn_rssEnabled'			=> 'bool',
			'fvcn_uploadImage'			=> 'bool',
			'fvcn_captchaLength'		=> 'int',
			'fvcn_maxImageW'			=> 'int',
			'fvcn_maxImageH'			=> 'int',
			'fvcn_numRSSItems'			=> 'int',
			'fvcn_numSubmissions'		=> 'int',
			'fvcn_maxDescriptionLength'	=> 'int',
			'fvcn_maxTitleLength'		=> 'int',
			'fvcn_captchaBgColor'		=> 'string',
			'fvcn_captchaLColor'		=> 'string',
			'fvcn_captchaTsColor'		=> 'string',
			'fvcn_captchaTColor'		=> 'string',
			'fvcn_titleBreaker'			=> 'string',
			'fvcn_descriptionBreaker'	=> 'string',
			'fvcn_submissionTemplate'	=> 'string',
			'fvcn_rssLocation'			=> 'string',
			'fvcn_akismetApiKey'		=> 'string'
			);
		
		foreach ($settings as $setting=>$type) {
			switch ($type) {
				case 'bool' :
					if (empty($_POST[ $setting ]))
						$_POST[ $setting ] = false;
					update_option($setting, (bool)$_POST[ $setting ]);
					break;
				case 'int' :
					if (empty($_POST[ $setting ]))
						$_POST[ $setting ] = 0;
					update_option($setting, abs( (int)$_POST[ $setting ] ));
					break;
				case 'string' :
				default :
					if (empty($_POST[ $setting ]))
						$_POST[ $setting ] = '';
					update_option($setting, (string)$_POST[ $setting ]);
					break;
			}
		}
		
		
		if (!$error)
			echo '<div id="message" class="updated fade"><p>Settings updated.</p></div>';
		else
			echo '<div class="error"><ul><li><strong>ERROR: </strong>' . implode('</li><li><strong>ERROR: </strong>', $error) . '</li></ul></div>';
	}
		
	if ('0' == get_option('fvcn_uploadDir') && '1' == get_option('fvcn_uploadImage'))
		echo '<div class="error"><ul><li><strong>ERROR: </strong>Failed to create image dir, please create it manualy.</li></ul></div>';
	?>
	<div id="tab-interface" class="wrap">
		<h2>Community News Settings</h2>
		<ul class="subsubsub">
			<li><a href="#general" rel="#general" class="tab current">General</a> |</li>
			<li><a href="#antispam" rel="#antispam" class="tab">Spam Protection</a> |</li>
			<li><a href="#template" rel="#template" class="tab">Template</a> |</li>
			<li><a href="#images" rel="#images" class="tab">Image Uploading</a> |</li>
			<li><a href="#rss" rel="#rss" class="tab">RSS</a></li>
		</ul>
		<br class="clear" />
		
		<form method="post" action="" id="tabContainer" enctype="multipart/form-data">
			<?php wp_nonce_field('fvCommunityNews_changeSettings'); ?>
			
			<div id="general" class="tabdiv currentTab">
				<h3>General Settings</h3>
				<p>General Settings</p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Before a submission appears</th>
						<td><fieldset>
								<legend class="hidden">Before a submission appears</legend>
								<label for="fvcn_alwaysAdmin">
									<input type="checkbox" name="fvcn_alwaysAdmin" id="fvcn_alwaysAdmin" value="1"<?php if (get_option('fvcn_alwaysAdmin')) echo ' checked="checked"'; ?> />
									An administrator must always approve the submission.</label>
								<br />
								<label for="fvcn_previousApproved">
									<input type="checkbox" name="fvcn_previousApproved" id="fvcn_previousApproved" value="1"<?php if (get_option('fvcn_previousApproved')) echo ' checked="checked"'; ?> />
									Submission author must have a previously approved submission.</label>
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row">E-mail me whenever</th>
						<td><fieldset>
								<legend class="hidden">E-mail me whenever</legend>
								<label for="fvcn_mailOnSubmission">
									<input type="checkbox" name="fvcn_mailOnSubmission" id="fvcn_mailOnSubmission" value="1"<?php if (get_option('fvcn_mailOnSubmission')) echo ' checked="checked"'; ?> />
									Anyone posts a submission.</label>
								<br />
								<label for="fvcn_mailOnModeration">
									<input type="checkbox" name="fvcn_mailOnModeration" id="fvcn_mailOnModeration" value="1"<?php if (get_option('fvcn_mailOnModeration')) echo ' checked="checked"'; ?> />
									A submission is held for moderation.</label>
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_maxTitleLength">Maximum Title Length</label></th>
						<td><input type="text" name="fvcn_maxTitleLength" id="fvcn_maxTitleLength" value="<?php echo get_option('fvcn_maxTitleLength'); ?>" size="4" /> Chars</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_titleBreaker">Break Title With</label></th>
						<td><input type="text" name="fvcn_titleBreaker" id="fvcn_titleBreaker" value="<?php echo get_option('fvcn_titleBreaker'); ?>" size="6" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_maxDescriptionLength">Maximum Description Length</label></th>
						<td><input type="text" name="fvcn_maxDescriptionLength" id="fvcn_maxDescriptionLength" value="<?php echo get_option('fvcn_maxDescriptionLength'); ?>" size="4" /> Chars</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_descriptionBreaker">Break Description With</label></th>
						<td><input type="text" name="fvcn_descriptionBreaker" id="fvcn_descriptionBreaker" value="<?php echo get_option('fvcn_descriptionBreaker'); ?>" size="6" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">My Submissions</th>
						<td><fieldset>
								<legend class="hidden">My Submissions</legend>
								<label for="fvcn_mySubmissions">
									<input type="checkbox" name="fvcn_mySubmissions" id="fvcn_mySubmissions" value="1"<?php if (get_option('fvcn_mySubmissions')) echo ' checked="checked"'; ?> />
									Add a `My Submissions` page where registered users could view and add their submissions.</label>
							</fieldset></td>
					</tr>
				</table>
			</div>
			
			<div id="antispam" class="tabdiv">
				<h3>Spam Protection</h3>
				<p>Get rid of those damm spambots. (Some default protection is already build-in)</p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Akismet</th>
						<td><fieldset>
								<legend class="hidden">Akismet</legend>
								<label for="fvcn_akismetEnabled">
									<input type="checkbox" name="fvcn_akismetEnabled" id="fvcn_akismetEnabled" value="1"<?php if (get_option('fvcn_akismetEnabled')) echo ' checked="checked"'; ?> />
									Enable Akismet spam protection.</label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_akismetApiKey">WordPress.com API Key</label></th>
						<td><input type="text" name="fvcn_akismetApiKey" id="fvcn_akismetApiKey" value="<?php echo get_option('fvcn_akismetApiKey'); ?>" /> <a href="http://wordpress.com/api-keys/" target="_blank">Get a key</a> (<a href="http://faq.wordpress.com/2005/10/19/api-key/" target="_blank">What is this?</a>)</td>
					</tr>
					<tr valign="top">
						<th scope="row">Authentication</th>
						<td><fieldset>
								<legend class="hidden">Authentication</legend>
								<label for="fvcn_loggedIn">
									<input type="checkbox" name="fvcn_loggedIn" id="fvcn_loggedIn" value="1"<?php if (get_option('fvcn_loggedIn')) echo ' checked="checked"'; ?> />
									Submission author must be logged in.</label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row">Enable Captcha</th>
						<td><fieldset>
								<legend class="hidden">Enable a Captcha Image</legend>
								<label for="fvcn_captchaEnabled">
									<input type="checkbox" name="fvcn_captchaEnabled" id="fvcn_captchaEnabled" value="1"<?php if (get_option('fvcn_captchaEnabled')) echo ' checked="checked"'; ?> />
									Enable or disable the use of a captcha.</label>
								<br />
								<label for="fvcn_hideCaptchaLoggedIn">
									<input type="checkbox" name="fvcn_hideCaptchaLoggedIn" id="fvcn_hideCaptchaLoggedIn" value="1"<?php if (get_option('fvcn_hideCaptchaLoggedIn')) echo ' checked="checked"'; ?> />
									Remove captcha for users who are already logged in.</label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_captchaLength">Captcha length</label></th>
						<td><input type="text" name="fvcn_captchaLength" id="fvcn_captchaLength" value="<?php echo get_option('fvcn_captchaLength'); ?>" size="2" /> Chars</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_captchaBgColor">Background Color</label></th>
						<td>#
						<input type="text" name="fvcn_captchaBgColor" id="fvcn_captchaBgColor" value="<?php echo get_option('fvcn_captchaBgColor'); ?>" size="6" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_captchaTColor">Text Color</label></th>
						<td>#
						<input type="text" name="fvcn_captchaTColor" id="fvcn_captchaTColor" value="<?php echo get_option('fvcn_captchaTColor'); ?>" size="6" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_captchaTsColor">Textshadow Color</label></th>
						<td>#
						<input type="text" name="fvcn_captchaTsColor" id="fvcn_captchaTsColor" value="<?php echo get_option('fvcn_captchaTsColor'); ?>" size="6" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_captchaLColor">Line Color</label></th>
						<td>#
						<input type="text" name="fvcn_captchaLColor" id="fvcn_captchaLColor" value="<?php echo get_option('fvcn_captchaLColor'); ?>" size="6" /></td>
					</tr>
				</table>
			</div>
			
			<div id="template" class="tabdiv">
				<h3>Template</h3>
				<p>These settings could be overwritten with values in your template tags.</p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="fvcn_numSubmissions">Number of Submissions</label></th>
						<td><input type="text" name="fvcn_numSubmissions" id="fvcn_numSubmissions" value="<?php echo get_option('fvcn_numSubmissions'); ?>" size="2" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">Submission Template</th>
						<td><fieldset>
								<legend class="hidden">Comment Blacklist</legend>
								<p>
									<label for="fvcn_submissionTemplate">The template for a single submission.<br />
									You can use the following tags: <strong>%submission_author%</strong>, <strong>%submission_author_email%</strong>, <strong>%submission_title%</strong>, <strong>%submission_url%</strong>, <strong>%submission_description%</strong>, <strong>%submission_date%</strong>.</label>
								</p>
								<p>
									<textarea name="fvcn_submissionTemplate" id="fvcn_submissionTemplate" cols="60" rows="10" style="width: 98%; font-size: 12px;" class="code"><?php echo stripslashes(get_option('fvcn_submissionTemplate')); ?></textarea>
							</p>
							</fieldset></td>
					</tr>
				</table>
			</div>
			
			<div id="images" class="tabdiv">
				<h3>Images</h3>
				<p>It is possible to allow people to upload an image together with their submission.</p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Enable Image Uploading</th>
						<td><fieldset>
								<legend class="hidden">Enable the RSS Feed</legend>
								<label for="fvcn_uploadImage">
									<input type="checkbox" name="fvcn_uploadImage" id="fvcn_uploadImage" value="1"<?php if (get_option('fvcn_uploadImage')) echo ' checked="checked"'; ?> />
									Allow people to upload an image.</label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_defaultImage">Default Image</label></th>
						<td>
							<input type="file" name="fvcn_defaultImage" id="fvcn_defaultImage" value="" /> The image that will be used if no image is uploaded.<br /><br />
							<?php $image = ('default'==get_option('fvcn_defaultImage')?WP_PLUGIN_URL.'/fv-community-news/images/default.png':get_option('home').'/wp-fvcn-images/default.'.get_option('fvcn_defaultImage')); ?>
							<img src="<?php echo $image; ?>" alt="" /><br /><small>Current default image.</small>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Max Image Size</th>
						<td>
							<span style="float:left;width:45px;padding:4px 0">Width:</span><input type="text" name="fvcn_maxImageW" id="fvcn_maxImageW" value="<?php echo get_option('fvcn_maxImageW'); ?>" size="5" /> pixels<br class="clear" />
							<span style="float:left;width:45px;padding:4px 0">Height:</span><input type="text" name="fvcn_maxImageH" id="fvcn_maxImageH" value="<?php echo get_option('fvcn_maxImageH'); ?>" size="5" /> pixels<br class="clear" />&nbsp; &nbsp;(0 = Unlimited)
						</td>
					</tr>
				</table>
			</div>
			
			<div id="rss" class="tabdiv">
				<h3>RSS</h3>
				<p>Configure your Community News RSS 2.0 feed.</p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Enable RSS Feed</th>
						<td><fieldset>
								<legend class="hidden">Enable the RSS Feed</legend>
								<label for="fvcn_rssEnabled">
									<input type="checkbox" name="fvcn_rssEnabled" id="fvcn_rssEnabled" value="1"<?php if (get_option('fvcn_rssEnabled')) echo ' checked="checked"'; ?> />
									Enable or disable the RSS 2.0 Feed.</label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_numRSSItems">Number of RSS Items</label></th>
						<td><input type="text" name="fvcn_numRSSItems" id="fvcn_numRSSItems" value="<?php echo get_option('fvcn_numRSSItems'); ?>" size="3" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_rssLocation">RSS Location</label></th>
						<td><?php
						if ($wp_rewrite->using_permalinks())
							echo get_option('home') . '/' . str_replace('feed/%feed%', '', $wp_rewrite->get_feed_permastruct());
						else
							echo get_option('home') . '/?feed=';
						?><input type="text" name="fvcn_rssLocation" id="fvcn_rssLocation" value="<?php echo get_option('fvcn_rssLocation'); ?>" style="padding:0" /></td>
					</tr>
				</table>
			</div>
			
			<p class="submit">
				<input type="submit" class="button-primary" name="Submit" value="Save Changes" />
			</p>
		</form>
	</div>
	<?php
}

?>