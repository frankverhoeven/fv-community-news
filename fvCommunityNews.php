<?php
/**
 *		Plugin Name:		FV Community News
 *		Plugin URI:			http://www.frank-verhoeven.com/wordpress-plugin-fv-community-news/
 *		Description:		Let visiters of your site post their articles on your site. Like this plugin? Please consider <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=SB62B7H867Y4C&lc=US&item_name=Frank%20Verhoeven&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted">making a small donation</a>.
 *		Version:			1.3.1
 *		Author:				Frank Verhoeven
 *		Author URI:			http://www.frank-verhoeven.com/
 *		
 *		@package			FV Community News
 *		@version			1.3.1
 *		@author				Frank Verhoeven
 *		@copyright			Coyright (c) 2008, Frank Verhoeven
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
 *		@var array $fvCommunityNewsErrorFields Fields with invallid validation.
 */
$fvCommunityNewsErrorFields = array();

/**
 *		@var int $fvCommunityNewsVersion Current version of FV Community News.
 */
$fvCommunityNewsVersion = '1.3.1';

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
	add_filter('the_content', 'fvCommunityNewsPostTags');
	
		// Initialization
	load_plugin_textdomain('fvcn', 'wp-content/plugins/fv-community-news/languages');
	
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
 *		Include the form for submitting news.
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
 *		@version 1.2
 */
function fvCommunityNewsHead() {
	global $wp_rewrite, $fvCommunityNewsVersion;
	$dir = WP_PLUGIN_URL . '/fv-community-news/javascript/';
	
	echo "\n\t\t" . '<script type="text/javascript" src="' . get_option('home') . '/wp-includes/js/jquery/jquery.js?ver=1.2.6"></script>' . "\n";
	echo "\t\t" . '<script type="text/javascript" src="' . $dir . 'fvCommunityNews.js"></script>' . "\n";
	if (get_option('fvcn_rssEnabled')) {
		if ($wp_rewrite->using_permalinks())
			$location = get_option('home') . '/' . str_replace('feed/%feed%', '', $wp_rewrite->get_feed_permastruct());
		else
			$location = get_option('home') . '/?feed=';
		$location .= get_option('fvcn_rssLocation');
		
		echo "\t\t" . '<link rel="alternate" type="application/rss+xml" title="' . get_option('blogname') . ' Community News RSS Feed" href="' . $location . '" />' . "\n";
	}
	if (get_option('fvcn_incStyle'))
		echo "\t\t" . '<link rel="stylesheet" type="text/css" href="' . WP_PLUGIN_URL . '/fv-community-news/styles/fvCommunityNewsStyles.css" />' . "\n";
		
	echo "\t\t" . '<meta name="Community-News-Creator" content="FV Community News - ' . $fvCommunityNewsVersion . '" />' . "\n\n";
}

/**
 *		Install the application.
 *		@version 1.3
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
	
	
	add_option('fvcn_captchaEnabled', false);
	add_option('fvcn_hideCaptchaLoggedIn', true);
	add_option('fvcn_captchaLength', 6);
	add_option('fvcn_captchaBgColor', 'ffffff');
	add_option('fvcn_captchaLColor', 'ffffff');
	add_option('fvcn_captchaTsColor', '686868');
	add_option('fvcn_captchaTColor', '0066cc');
	add_option('fvcn_alwaysAdmin', false);
	add_option('fvcn_previousApproved', true);
	add_option('fvcn_mailOnSubmission', false);
	add_option('fvcn_mailOnModeration', true);
	add_option('fvcn_maxTitleLength', '50');
	add_option('fvcn_titleBreaker', '&hellip;');
	add_option('fvcn_maxDescriptionLength', '200');
	add_option('fvcn_descriptionBreaker', '&hellip;');
	add_option('fvcn_numSubmissions', '');
	add_option('fvcn_submissionTemplate', '');
	add_option('fvcn_formTitle', 'Add News');
	add_option('fvcn_submissionsTitle', 'Community News');
	add_option('fvcn_rssEnabled', true);
	add_option('fvcn_numRSSItems', 10);
	add_option('fvcn_rssLocation', 'community-news.rss');
	add_option('fvcn_loggedIn', false);
	add_option('fvcn_uploadImage', false);
	add_option('fvcn_maxImageW', 45);
	add_option('fvcn_maxImageH', 45);
	add_option('fvcn_mySubmissions', false);
	add_option('fvcn_akismetEnabled', false);
	add_option('fvcn_defaultImage', 'default');
	add_option('fvcn_incStyle', true);
	add_option('fvcn_responseOversizedImage', 'The image you are trying to upload is too big.');
	add_option('fvcn_responseInvalidImage', 'The file you are trying to upload isn\'t allowed.');
	add_option('fvcn_responseInvalidEmail', 'Please enter a valid email address.');
	add_option('fvcn_responseEmpty', 'You didn\'t fill in all required fields.');
	add_option('fvcn_responseSuccess', 'Your submission has been added. Thank you!');
	add_option('fvcn_responseInvalidCaptcha', 'You didn\'t fill in a valid captcha value.');
	add_option('fvcn_responseBumping', 'You can only add one submission each two minutes.');
	add_option('fvcn_responseLoggedIn', 'You must be logged in to add a submission.');
	add_option('fvcn_responseFailure', 'Unable to add your submission, please try again later.');
	add_option('fvcn_responseModeration', 'Your submission has been added to the moderation queue and will appear soon. Thank you!');
	
	$akismetApiKey = '';
	if (get_option('wordpress_api_key'))
		$akismetApiKey = get_option('wordpress_api_key');
	add_option('fvcn_akismetApiKey', $akismetApiKey);
	
	if (fvCommunityNewsMakeDirectory(ABSPATH . 'wp-fvcn-images'))
		add_option('fvcn_uploadDir', true);
	else
		add_option('fvcn_uploadDir', false);
}

/**
 *		Update the application.
 *		@since 1.1
 *		@version 1.2
 */
function fvCommunityNewsUpdate() {
	global $fvCommunityNewsVersion;
	
	update_option('fvcn_version', $fvCommunityNewsVersion);
	
	// Version 1.1
	add_option('fvcn_rssEnabled', true);
	add_option('fvcn_numRSSItems', 10);
	add_option('fvcn_rssLocation', 'community-news.rss');
	add_option('fvcn_loggedIn', false);
	add_option('fvcn_hideCaptchaLoggedIn', true);
	
	// Version 1.2
	add_option('fvcn_mySubmissions', false);
	add_option('fvcn_uploadImage', false);
	add_option('fvcn_maxImageW', 45);
	add_option('fvcn_maxImageH', 45);
	add_option('fvcn_akismetEnabled', false);
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
		add_option('fvcn_uploadDir', true);
	else
		add_option('fvcn_uploadDir', false);
	
	// Version 1.3
	add_option('fvcn_incStyle', false);
	add_option('fvcn_responseOversizedImage', 'The image you are trying to upload is too big.');
	add_option('fvcn_responseInvalidImage', 'The file you are trying to upload isn\'t allowed.');
	add_option('fvcn_responseInvalidEmail', 'Please enter a valid email address.');
	add_option('fvcn_responseEmpty', 'You didn\'t fill in all required fields.');
	add_option('fvcn_responseSuccess', 'Your submission has been added. Thank you!');
	add_option('fvcn_responseInvalidCaptcha', 'You didn\'t fill in a valid captcha value.');
	add_option('fvcn_responseBumping', 'You can only add one submission each two minutes.');
	add_option('fvcn_responseLoggedIn', 'You must be logged in to add a submission.');
	add_option('fvcn_responseFailure', 'Unable to add your submission, please try again later.');
	add_option('fvcn_responseModeration', 'Your submission has been added to the moderation queue and will appear soon. Thank you!');
}

/**
 *		Create a directory.
 *		@param string $dirPath The location of the dir we want to create.
 *		@param int $chomd An octal number witch contains the chmod info.
 *		@return bool Success or failed.
 *		@version 1.1
 *		@since 1.2
 */
function fvCommunityNewsMakeDirectory($dirPath, $chmod=0777) {
	$dirPath = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $dirPath), "/");
	$e = explode("/", ltrim($dirPath, "/"));
	if(substr($dirPath, 0, 1) == "/") {
		$e[0] = "/".$e[0];
	}
	$c = count($e);
	$cp = $e[0];
	for($i = 1; $i < $c; $i++) {
		if(!is_dir($cp) && !@mkdir($cp, $chmod)) {
			return false;
		}
		$cp .= "/".$e[$i];
	}
	return @mkdir($dirPath, $chmod);
}

/**
 *		Removes a directory.
 *		@param string $path The location of the dir we want to remove.
 *		@return bool Success or failed.
 *		@version 1.0
 *		@since 1.3
 */
function fvCommunityNewsRemoveDirectory($path) {
	if (is_file($path)) {
		if (is_writable($path)) {
			if (@unlink($path)) {
				return true;
			}
		}
		return false;
	}
	if (is_dir($path)) {
		if (is_writeable($path)) {
			foreach (new DirectoryIterator($path) as $res) {
				if ($res->isDot()) {
					unset($res);
					continue;
				}
				if ($res->isFile()) {
					fvCommunityNewsRemoveDirectory($res->getPathName());
				} elseif ($res->isDir()) {
					fvCommunityNewsRemoveDirectory($res->getRealPath());
				}
				unset($res);
			}
			if (@rmdir($path)) {
				return true; 
			}
		}
		return false;
	}
}

/**
 *		Check if the uploaded image is valid.
 *		@param array $file The uploaded file.
 *		@return bool Valid image true, else false.
 *		@version 1.0.1
 *		@since 1.2.1
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
	
	if (!array_key_exists($ext, $allowedImageTypes) || $allowedImageTypes[ $ext ] != $imageInfo['mime'] || !is_uploaded_file($file['tmp_name']) || UPLOAD_ERR_OK != $file['error']) {
		$fvCommunityNewsSubmitError = get_option('fvcn_responseInvalidImage');
		return false;
	}
	
	if ( (('0' != get_option('fvcn_maxImageW') && $imageInfo[0] > (int)get_option('fvcn_maxImageW')) || 
		 ('0' != get_option('fvcn_maxImageH') && $imageInfo[1] > (int)get_option('fvcn_maxImageH'))) ||
		 filesize($file['tmp_name']) > 2048000 && !$ignoreSize) {
		$fvCommunityNewsSubmitError = get_option('fvcn_responseOversizedImage');
		return false;
	}
	
	return true;
}

/**
 *		A submission is posted and handled here.
 *		@return bool True if the submission is successfull posted, false otherwise.
 *		@version 1.3
 */
function fvCommunityNewsSubmit() {
	global	$fvCommunityNewsSubmited,
			$fvCommunityNewsSubmitError,
			$fvCommunityNewsFieldValues,
			$fvCommunityNewsAwaitingModeration,
			$fvCommunityNewsErrorFields,
			$wpdb;
	
	$fvCommunityNewsSubmited = true;
	
	if (get_option('fvcn_loggedIn') && !is_user_logged_in()) {
		$fvCommunityNewsSubmitError = get_option('fvcn_responseLoggedIn');
		return false;
	}
	
	if (isset($_SESSION['fvCommunityNewsLastPost']) && $_SESSION['fvCommunityNewsLastPost'] > current_time('timestamp')) {
		$fvCommunityNewsSubmitError = get_option('fvcn_responseBumping');
		return false;
	}
	
	if (!empty($_POST['fvCommunityNewsPhone']) || !check_admin_referer('fvCommunityNews_addSubmission')) {
		$fvCommunityNewsSubmitError = 'Spambots are not allowed!';
		return false;
	}
	
	$fields = array('fvCommunityNewsName',
					'fvCommunityNewsEmail',
					'fvCommunityNewsTitle',
					'fvCommunityNewsDescription');
	if (get_option('fvcn_captchaEnabled') && !(get_option('fvcn_hideCaptchaLoggedIn') && is_user_logged_in()))
		$fields[] = 'fvCommunityNewsCaptcha';
	
	
	foreach ($fields as $field) {
		if (empty($_POST[ $field ]) || $_POST[ $field ] == $fvCommunityNewsFieldValues[ $field ]) {
			$fvCommunityNewsErrorFields[] = $field;
			$fvCommunityNewsSubmitError = get_option('fvcn_responseEmpty');
		}
	}
	if (false != $fvCommunityNewsSubmitError)
		return false;
	
	if (get_option('fvcn_captchaEnabled') && !(get_option('fvcn_hideCaptchaLoggedIn') && is_user_logged_in())) {
		if (sha1($_POST['fvCommunityNewsCaptcha']) != $_SESSION['fvCommunityNewsCaptcha']) {
			$fvCommunityNewsSubmitError = get_option('fvcn_responseInvalidCaptcha');
			$fvCommunityNewsErrorFields[] = 'fvCommunityNewsCaptcha';
			return false;
		}
		
		unset($_SESSION['fvCommunityNewsCaptcha']);
	}
	
	if (!is_email($_POST['fvCommunityNewsEmail'])) {
		$fvCommunityNewsSubmitError = get_option('fvcn_responseInvalidEmail');
		$fvCommunityNewsErrorFields[] = 'fvCommunityNewsEmail';
		return false;
	}
	
	if (get_option('fvcn_uploadImage') && isset($_FILES['fvCommunityNewsImage'], $_POST['fvCommunityNewsImageCheck']) && !empty($_FILES['fvCommunityNewsImage']['name'])) {
		if (!fvCommunityNewsCheckImageUpload($_FILES['fvCommunityNewsImage'])) {
			$fvCommunityNewsErrorFields[] = 'fvCommunityNewsImage';
			return false;
		}
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
		   'comment_author'			=> $_POST['fvCommunityNewsName'],
		   'comment_author_email'	=> $_POST['fvCommunityNewsEmail'],
		   'comment_content'		=> $_POST['fvCommunityNewsDescription'],
		   'user_ip'				=> $_SERVER['REMOTE_ADDR'],
		   'user_agent'				=> $_SERVER['HTTP_USER_AGENT']
		);
		
		foreach ($_SERVER as $key=>$val) {
			$submission[ $key ] = $val;
		}
		
		$GLOBALS['akismet_key']		= get_option('fvcn_akismetApiKey');
		$GLOBALS['akismet_home']	= get_option('home');
		$GLOBALS['akismet_ua']		= $_SERVER['HTTP_USER_AGENT'];
	
		if(akismet_check($submission)) {
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
		$fvNewPosterSubmitError = get_option('fvcn_responseFailure');
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
 *		@version 1.1
 *		@since 1.2
 */
function fvCommunityNewsAjaxResponse() {
	global $fvCommunityNewsErrorFields;
	
	if (!headers_sent())
		header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
	
	$response = '<fvCommunityNewsAjaxResponse>';
	
	if (fvCommunityNewsSubmitError()) {
		$response .= '<status>error</status>';
		$response .= '<message>' . fvCommunityNewsSubmitError() . '</message>';
		$response .= '<errorfields>';
		foreach ($fvCommunityNewsErrorFields as $field) {
			$response .= '<field>' . $field . '</field>';
		}
		$response .= '</errorfields>';
	} elseif (fvCommunityNewsAwaitingModeration()) {
		$response .= '<status>moderation</status>';
		$response .= '<message>' . get_option('fvcn_responseModeration') . '</message>';
	} else {
		$response .= '<status>approved</status>';
		$response .= '<message>' . get_option('fvcn_responseSuccess') . '</message>';
	}
	
	$response .= '</fvCommunityNewsAjaxResponse>';
	
	die ($response);
}

/**
 *		Get the error fields.
 *		@since 1.3
 *		@version 1.0
 */
function fvCommunityNewsGetErrorFields() {
	global $fvCommunityNewsErrorFields;
	return $fvCommunityNewsErrorFields;
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
 *		@version 1.2.1
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
			$format = '<li><strong><a href="%submission_url%" title="%submission_title%">%submission_title%</a></strong><small>%submission_date%</small><br />%submission_description%</li>';
	}
	
	$sql = "SELECT
				*
			FROM
				" . get_option('fvcn_dbname') . "
			WHERE
				Approved = '1'
			ORDER BY
				Date DESC
			LIMIT
				0, " . (int)$wpdb->escape($number);
	
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
		<label for="fvcn_formTitle"><?php _e('Title', 'fvcn'); ?>
			<input type="text" id="fvcn_formTitle" name="fvcn_formTitle" value="<?php echo get_option('fvcn_formTitle'); ?>" class="widefat" />
		</label>
		
		<label><?php _e('Description', 'fvcn'); ?>
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
		<label for="fvcn_submissionsTitle"><?php _e('Title', 'fvcn'); ?>
		<input type="text" id="fvcn_submissionsTitle" name="fvcn_submissionsTitle" value="<?php echo get_option('fvcn_submissionsTitle'); ?>" class="widefat" />
		</label>
		
		<label><?php _e('Description', 'fvcn'); ?>
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
	
	add_menu_page(__('Manage Submissions', 'fvcn'), __('Submissions', 'fvcn') . ('0' != $total?' <span id="awaiting-mod" class="count-' . $total . '"><span class="submission-count">' . $total . '</span></span>':''), 'moderate_comments', dirname(__FILE__), 'fvCommunityNewsSubmissions');
	add_submenu_page(dirname(__FILE__), __('Community News Settings', 'fvcn'), __('Settings', 'fvcn'), 'manage_options', 'fvCommunityNewsSettings', 'fvCommunityNewsSettings');
	add_submenu_page(dirname(__FILE__), __('Community News Uninstall', 'fvcn'), __('Uninstall', 'fvcn'), 'manage_options', 'fvCommunityNewsUninstall', 'fvCommunityNewsUninstall');
	
	// My Submissions
	if (get_option('fvcn_mySubmissions'))
		add_submenu_page('profile.php', __('My Submissions', 'fvcn'), __('My Submissions', 'fvcn'), 'read', 'fvCommunityNewsMySubmissions', 'fvCommunityNewsMySubmissions');
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
			$submenu['fv-community-news'][ $key ] = preg_replace('/' . __('Submissions', 'fvcn') . ' <span id="awaiting-mod" class="count-\d"><span class="submission-count">\d<\/span><\/span>/', 'Submissions', $item);
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
		$links = array_merge(array('<a href="' . attribute_escape('admin.php?page=fvCommunityNewsSettings') . '">' . __('Settings', 'fvcn') . '</a>'), $links);
	
	return $links;
}

/**
 *		Add a dashboard widget to the dashboard.
 *		@version 1.0.1
 *		@since 1.2
 */
function fvCommunityNewsAddDashboard() {
	if (current_user_can('moderate_comments') && function_exists('wp_add_dashboard_widget'))
		wp_add_dashboard_widget('fvCommunityNewsDashboard', 'Submissions <a href="admin.php?page=fv-community-news" class="edit-box open-box">' . __('View All', 'fvcn') . '</a>', 'fvCommunityNewsDashboard');
}

/**
 *		Add current stats to the Right Now section.
 *		@version 1.1
 *		@since 1.2.2
 */
function fvCommunityNewsDashboard() {
	
	list($submissions, $total) = fvCommunityNewsGetSubmissionsList(false, 0, 5);
	
	if ($submissions) {
		echo ' <div id="the-submission-list" class="list:submissions">';
		$i = 0;
		
		foreach ($submissions as $submission) :
			echo '<div id="submission-' . $submission->Id . '" class="submission-item ' . ($submission->Approved?'approved':'unapproved') . ' ' . ($i&1?'odd alt':'even') . '">';
				$i++;
				echo get_avatar(stripslashes(apply_filters('get_submission_author_email', $submission->Email)), 50);
				echo '<h4 class="submission-meta">' . __('From', 'fvcn') . ' <cite class="submission-author">' . stripslashes(apply_filters('get_submission_author', $submission->Name)) . '</cite> ';
				echo ' ' . __('linking to', 'fvcn') . ' <a href="' . stripslashes(apply_filters('submission_author_url', $submission->Location)) . '">' . stripslashes(apply_filters('get_submission_author', $submission->Title)) . '</a></h4>';
				echo '<blockquote><p>' . trim( stripslashes(apply_filters('submission_text', $submission->Description)) ) . '</p></blockquote>';
				echo '<p class="submission-actions">';
					
					echo '<span class="approve"><a href="';
					echo wp_nonce_url('?fvCommunityNewsAdminAction=approvesubmission&amp;s=' . $submission->Id, 'fvCommunityNews_approveSubmission' . $submission->Id);
					echo '" title="' . __('Approve this submission', 'fvcn') . '">' . __('Approve', 'fvcn') . '</a></span>';
					
					echo '<span class="unapprove"><a href="';
					echo wp_nonce_url('?fvCommunityNewsAdminAction=unapprovesubmission&amp;s=' . $submission->Id, 'fvCommunityNews_unapproveSubmission' . $submission->Id);
					echo '" title="' . __('Unapprove this submission', 'fvcn') . '">' . __('Unapprove', 'fvcn') . '</a></span>';
					
					echo '<span class="edit"> | <a href="admin.php?page=fv-community-news&amp;mode=edit-submission&amp;submission=' . $submission->Id . '" title="Edit submission">Edit</a></span>';
					
					echo '<span class="spam"> | <a href="';
					echo wp_nonce_url('?fvCommunityNewsAdminAction=spamsubmission&amp;s=' . $submission->Id, 'fvCommunityNews_spamSubmission' . $submission->Id);
					echo '" title="' . __('Mark this submission as spam', 'fvcn') . '">' . __('Spam', 'fvcn') . '</a> | </span>';
					
					echo '<span class="delete"><a href="';
					echo wp_nonce_url('?fvCommunityNewsAdminAction=deletesubmission&amp;s=' . $submission->Id, 'fvCommunityNews_deleteSubmission' . $submission->Id);
					echo '" title="' . __('Delete this submission', 'fvcn') . '">' . __('Delete', 'fvcn') . '</a></span>';
					
				echo '</p>';
			echo '</div>' . "\n";
		endforeach;

		
		echo '</div>' . "\n\n";
		
	} else {
		echo '<p>' . __('No submissions yet.', 'fvcn') . '</p>';
	}
	
}

/**
 *		Create tags to use in posts/pages.
 *		@param string $content The Post/Page Content.
 *		@return string The Post/Page Content.
 *		@version 1.0
 *		@since 1.3
 */
function fvCommunityNewsPostTags($content) {
	
	if (strstr($content, '<!--fvCommunityNews:Form-->')) {
		ob_start();
		fvCommunityNewsForm();
		$form = ob_get_contents();
		ob_end_clean();
		$content = str_replace('<!--fvCommunityNews:Form-->', $form, $content);
	}
	if (strstr($content, '<!--fvCommunityNews:Submissions-->')) {
		$content = str_replace('<!--fvCommunityNews:Submissions-->', fvCommunityNewsGetSubmissions(), $content);
	}
	
	return $content;
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
	
	echo '<script type="text/javascript" src="' . $dir . 'javascript/fvCommunityNewsAdmin.js"></script>' . "\n";
	echo '<link rel="stylesheet" href="' . $dir . 'styles/fvCommunityNewsAdmin.css" />' . "\n";
}

/**
 *		Admin page for viewing `My Submissions`
 *		@version 1.0.2
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
	
	echo '<div class="wrap"><h2>' . __('My Submissions', 'fvcn') . '</h2><p>' . __('The Community News you have added.', 'fvcn') . '';
	
	if (empty($submissions)) :
		echo '<p>' . __('No submissions here, yet.', 'fvcn') . '</p>';
	else : ?>
	
		<?php if ( $pageLinks ) : ?>
		<div class="tablenav">
			<?php
			echo '<div class="tablenav-pages">' .  sprintf( '<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s', 'fvcn') . '</span>%s',
				number_format_i18n( $start + 1 ),
				number_format_i18n( min( $page * $submissionPerPage, $total ) ),
				number_format_i18n( $total ),
				$pageLinks) . '</div>';
			?>
			<br class="clear" />
		</div>
		<?php endif; ?>
		
		<table class="widefat fixed" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="comment" class="manage-column column-comment" style=""><?php _e('Submission', 'fvcn'); ?></th>
					<th scope="col" id="author" class="manage-column column-author" style="min-width: 210px;"><?php _e('Author', 'fvcn'); ?></th>
					<?php if (get_option('fvcn_uploadImage'))
						echo '<th scope="col" id="author" class="manage-column column-image">' . __('Image', 'fvcn') . '</th>'; ?>
					<th scope="col" id="date" class="manage-column column-date" style="min-width: 120px;"><?php _e('Submitted', 'fvcn'); ?></th>
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
					<th scope="col" class="manage-column column-comment" style=""><?php _e('Submission', 'fvcn'); ?></th>
					<th scope="col" class="manage-column column-author" style=""><?php _e('Author', 'fvcn'); ?></th>
					<?php if (get_option('fvcn_uploadImage'))
						echo '<th scope="col" id="author" class="manage-column column-image">' . __('Image', 'fvcn') . '</th>'; ?>
					<th scope="col" class="manage-column column-date" style=""><?php _e('Submitted', 'fvcn'); ?></th>
				</tr>
			</tfoot>
		</table>
	<?php endif; ?>
		
	<?php if ( $pageLinks ) : ?>
	<div class="tablenav">
		<?php
		echo '<div class="tablenav-pages">' .  sprintf( '<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s', 'fvcn') . '</span>%s',
			number_format_i18n( $start + 1 ),
			number_format_i18n( min( $page * $submissionPerPage, $total ) ),
			number_format_i18n( $total ),
			$pageLinks) . '</div>';
		?>
		<br class="clear" />
	</div>
	<?php endif; ?>
	<br />
	
	<h2><?php _e('Add News', 'fvcn'); ?></h2>
	<p><?php _e('Add a new submission.', 'fvcn'); ?></p>
	<?php fvCommunityNewsForm();
		
	echo '</div>';
}

/**
 *		Admin page for managing submissions.
 *		@version 1.3
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
			
			echo '<div id="moderated" class="updated fade"><p>' . count($_POST['submissions']) . ' ' . __('submissions', 'fvcn') . ' ';
			if (isset($_POST['submission-approve']) && !(isset($_POST['submission-unapprove']) || isset($_POST['submission-delete'])) )
				_e('approved', 'fvcn');
			if (isset($_POST['submission-unapprove']))
				_e('unapproved', 'fvcn');
			if (isset($_POST['submission-delete']))
				_e('deleted', 'fvcn');
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
			
			echo '<div id="moderated" class="updated fade"><p>' . __('Submission Updated.', 'fvcn') . '</p></div>';
			
		}
	}
	
	if ('0' == get_option('fvcn_uploadDir') && '1' == get_option('fvcn_uploadImage'))
			echo '<div class="error"><ul><li><strong>' . __('ERROR:', 'fvcn') . ' </strong>' . __('Failed to create image dir, please create it manualy.', 'fvcn') . '</li></ul></div>';
	?>
<div class="wrap">
		<?php if (!isset($_GET['mode'], $_GET['submission']) || $_GET['mode'] != 'edit-submission') : ?>
		
		<h2><?php _e('Manage Submissions', 'fvcn'); ?></h2>
		<ul class="subsubsub">
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'all', $_SERVER['REQUEST_URI'])) ?>" <?php if ('all' == $submissionStatus) echo 'class="current"' ?>><?php _e('Show All', 'fvcn'); ?></a> |</li>
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'moderation', $_SERVER['REQUEST_URI'])) ?>" <?php if ('moderation' == $submissionStatus) echo 'class="current"' ?>><?php _e('Awaiting Moderation', 'fvcn'); ?> <span class="count"><?php echo '(' . $numAwaitingMod . ')'; ?></span></a> |</li>
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'approved', $_SERVER['REQUEST_URI'])) ?>" <?php if ('approved' == $submissionStatus) echo 'class="current"' ?>><?php _e('Approved', 'fvcn'); ?></a> |</li>
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'spam', $_SERVER['REQUEST_URI'])) ?>" <?php if ('spam' == $submissionStatus) echo 'class="current"' ?>><?php _e('Spam', 'fvcn'); ?> <span class="count"><?php echo '(' . $numSpam . ')'; ?></span></a></li>
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
				echo '<br class="clear" /><p>' . __('No submissions here, must be your lucky day.', 'fvcn') . '</p>';
			else
				echo '<br class="clear" /><p>' . __('No submissions here, yet.', 'fvcn') . '</p>';
		else : ?>
		
		<form id="comments-form" action="" method="post">
			<div class="tablenav">
				<?php
				if ( $pageLinks )
					echo '<div class="tablenav-pages">' .  sprintf( '<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s', 'fvcn') . '</span>%s',
						number_format_i18n( $start + 1 ),
						number_format_i18n( min( $page * $submissionPerPage, $total ) ),
						number_format_i18n( $total ),
						$pageLinks) . '</div>';
				?>
				<div class="alignleft">
					<input type="submit" name="submission-approve" id="submission-approve" value="<?php _e('Approve' , 'fvcn'); ?>" class="button-secondary" />
					<input type="submit" name="submission-unapprove" id="submission-unapprove" value="<?php _e('Unapprove' , 'fvcn'); ?>" class="button-secondary" />
					<input type="submit" name="submission-spam" id="submission-spam" value="<?php _e('Spam' , 'fvcn'); ?>" class="button-secondary" />
					<input type="submit" name="submission-delete" id="submission-delete" value="<?php _e('Delete' , 'fvcn'); ?>" class="button-secondary delete" />
				</div>
				<br class="clear" />
			</div>
			<br class="clear" />
			
			<table class="widefat fixed" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" onclick="fvCommunityNewsCheckAll();" /></th>
					<th scope="col" id="comment" class="manage-column column-comment" style=""><?php _e('Submission' , 'fvcn'); ?></th>
					<th scope="col" id="author" class="manage-column column-author" style="min-width: 210px;"><?php _e('Author' , 'fvcn'); ?></th>
					<?php if (get_option('fvcn_uploadImage'))
						echo '<th scope="col" id="author" class="manage-column column-image">' . __('Image', 'fvcn') . '</th>'; ?>
					<th scope="col" id="date" class="manage-column column-date" style="min-width: 120px;"><?php _e('Submitted' , 'fvcn'); ?></th>
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
				echo '" title="' . __('Approve this submission' , 'fvcn') . '">' . __('Approve' , 'fvcn') . '</a></span>';

				echo '<span class="unapprove"><a href="';
				echo wp_nonce_url('?fvCommunityNewsAdminAction=unapprovesubmission&amp;s=' . $post->Id, 'fvCommunityNews_unapproveSubmission' . $post->Id);
				echo '" title="' . __('Unapprove this submission' , 'fvcn') . '">' . __('Unapprove' , 'fvcn') . '</a></span>';
				
				echo '<span class="edit"> | <a href="admin.php?page=fv-community-news&amp;mode=edit-submission&amp;submission=' . $post->Id . '" title="' . __('Edit submission' , 'fvcn') . '">' . __('Edit' , 'fvcn') . '</a></span>';
				
				if ('spam' != $post->Approved) {
					echo '<span class="spam"> | <a href="';
					echo wp_nonce_url('?fvCommunityNewsAdminAction=spamsubmission&amp;s=' . $post->Id, 'fvCommunityNews_spamSubmission' . $post->Id);
					echo '" title="' . __('Mark this submission as spam' , 'fvcn') . '">' . __('Spam' , 'fvcn') . '</a></span>';
				}
				
				echo '<span class="delete"> | <a href="';
				echo wp_nonce_url('?fvCommunityNewsAdminAction=deletesubmission&amp;s=' . $post->Id, 'fvCommunityNews_deleteSubmission' . $post->Id);
				echo '" title="' . __('Delete this submission' , 'fvcn') . '">' . __('Delete' , 'fvcn') . '</a></span>';
				
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
					<th scope="col" class="manage-column column-comment" style=""><?php _e('Submission' , 'fvcn'); ?></th>
					<th scope="col" class="manage-column column-author" style=""><?php _e('Author' , 'fvcn'); ?></th>
					<?php if (get_option('fvcn_uploadImage'))
						echo '<th scope="col" id="author" class="manage-column column-image">' . __('Image' , 'fvcn') . '</th>'; ?>
					<th scope="col" class="manage-column column-date" style=""><?php _e('Submitted' , 'fvcn'); ?></th>
				</tr>
			</tfoot>
			</table>
			<?php wp_nonce_field('fvCommunityNews_moderateSubmissions'); ?>
			<input type="hidden" name="fvCommunityNewsAdmin" id="fvCommunityNewsAdmin" value="true" />
		
			<div class="tablenav">
				<?php
				if ( $pageLinks )
					echo '<div class="tablenav-pages">' .  sprintf( '<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s', 'fvcn') . '</span>%s',
						number_format_i18n( $start + 1 ),
						number_format_i18n( min( $page * $submissionPerPage, $total ) ),
						number_format_i18n( $total ),
						$pageLinks) . '</div>';
				?>
				<div class="alignleft">
					<input type="submit" name="submission-approve" value="<?php _e('Approve' , 'fvcn'); ?>" class="button-secondary" />
					<input type="submit" name="submission-unapprove" value="<?php _e('Unapprove' , 'fvcn'); ?>" class="button-secondary" />
					<input type="submit" name="submission-spam" id="submission-spam" value="<?php _e('Spam' , 'fvcn'); ?>" class="button-secondary" />
					<input type="submit" name="submission-delete" value="<?php _e('Delete' , 'fvcn'); ?>" class="button-secondary delete" />
				</div>
				<br class="clear" />
			</div>
		</form>
		<?php endif; ?>
		
		<?php else : // Edit submission
		$submission = $wpdb->get_results("SELECT * FROM " . get_option('fvcn_dbname') . " WHERE Id = '" . $wpdb->escape($_GET['submission']) . "'");
		$submission =  $submission[0];
		?>
		<h2><?php _e('Edit Submission', 'fvcn'); ?></h2>
		<form name="post" action="" method="post" id="post">
			<?php wp_nonce_field('updateSubmission_' . $submission->Id); ?>
			<input type="hidden" name="submissionId" id="submissionId" value="<?php echo $submission->Id; ?>" />
			<div id="poststuff" class="metabox-holder">
				<?php
				$email = stripslashes(attribute_escape( $submission->Email ));
				$url = stripslashes(attribute_escape( $submission->Location ));
				?>
				<div id="side-info-column" class="inner-sidebar">
					<div id="submitdiv" class="stuffbox" >
						<h3><span class='hndle'><?php _e('Status', 'fvcn') ?></span></h3>
						<div class="inside">
							<div class="submitbox" id="submitcomment">
								<div id="minor-publishing">
									<div id="minor-publishing-actions">
										<div id="preview=action"> <a class="preview button" href="<?php echo clean_url('admin.php?page=fv-community-news'); ?>">Back</a> </div>
										<div class="clear"></div>
									</div>
									<div id="misc-publishing-actions">
										<div class="misc-pub-section" id="comment-status-radio">
											<label class="approved">
												<input type="radio"<?php checked( $submission->Approved, '1' ); ?> name="Approved" value="1" />
												<?php _e('Approved', 'fvcn'); ?></label>
											<br />
											<label class="waiting">
												<input type="radio"<?php checked( $submission->Approved, '0' ); ?> name="Approved" value="0" />
												<?php _e('Pending', 'fvcn'); ?></label>
											<br />
											<label class="spam">
												<input type="radio"<?php checked( $submission->Approved, 'spam' ); ?> name="Approved" value="spam" />
												<?php _e('Spam', 'fvcn'); ?></label>
										</div>
										<div class="misc-pub-section curtime misc-pub-section-last">
											<?php
											$datef = __( 'M j, Y @ G:i', 'fvcn');
											$stamp = __('Submitted on: <b>%1$s</b>', 'fvcn');
											$date = date_i18n( $datef, strtotime( $submission->Date ) );
											?>
											<span id="timestamp"><?php printf($stamp, $date); ?></span>
										</div>
									</div>
									<!-- misc actions -->
									<div class="clear"></div>
								</div>
								<div id="major-publishing-actions">
									<div id="delete-action"><?php echo "<a class='submitdelete deletion' href='" . wp_nonce_url('?fvCommunityNewsAdminAction=deletesubmission&amp;s=' . $_GET['submission'], 'fvCommunityNews_deleteSubmission' . $_GET['submission']) . "'>" . __('Delete', 'fvcn') . "</a>"; ?></div>
									<div id="publishing-action">
										<input type="submit" name="save" value="<?php _e('Save', 'fvcn'); ?>" tabindex="6" class="button-primary" />
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div>
					
					<?php if (get_option('fvcn_uploadImage')) : ?>
					<div id="imagediv" class="stuffbox" >
						<h3><span class="hndle"><?php _e('Image', 'fvcn') ?></span></h3>
						<div class="inside" style="text-align: center; overflow: auto">
							<?php echo ' <td class="image column-image"><img src="' . (NULL==$submission->Image?('default'==get_option('fvcn_defaultImage')?WP_PLUGIN_URL.'/fv-community-news/images/default.png':get_option('home').'/wp-fvcn-images/default.'.get_option('fvcn_defaultImage')):get_option('home').'/wp-fvcn-images/'.$submission->Image) . '" alt="" /></td>' . "\n"; ?>
						</div>
					</div>
					<?php endif; ?>
					
				</div>
				<div id="post-body" class="has-sidebar">
					<div id="post-body-content" class="has-sidebar-content">
						<div id="namediv" class="stuffbox">
							<h3>
								<label for="name"><?php _e('Author', 'fvcn') ?></label>
							</h3>
							<div class="inside">
								<table class="form-table">
									<tbody>
										<tr valign="top">
											<td class="first"><?php _e('Name', 'fvcn'); ?>:</td>
											<td><input type="text" name="Name" size="30" value="<?php echo stripslashes(attribute_escape( $submission->Name )); ?>" tabindex="1" id="Name" /></td>
										</tr>
										<tr valign="top">
											<td class="first">
												<?php
												if ( $email ) {
													printf( __('E-mail (%s):', 'fvcn'), '<a href="mailto:' . stripslashes(apply_filters('get_comment_author_email', $email)) . '">Send Email</a>' );
												} else {
													_e('E-mail:', 'fvcn');
												}
												?>
											</td>
											<td><input type="text" id="Email" name="Email" size="30" value="<?php echo $email; ?>" tabindex="2" id="email" /></td>
										</tr>
										<tr valign="top">
											<td class="first">
												<?php
												if ( ! empty( $url ) && 'http://' != $url ) {
													$link = "<a href='$url' rel='external nofollow' target='_blank'>" . __('visit site') . "</a>";
													printf( __('URL (%s):', 'fvcn'), apply_filters('get_comment_author_link', $link ) ); 
												} else {
													_e('URL:', 'fvcn');
												} ?>
											</td>
											<td><input type="text" id="Location" name="Location" size="30" value="<?php echo $url; ?>" tabindex="3" /></td>
										</tr>
										<tr valign="top">
											<td class="first"><?php _e('Post Title:', 'fvcn'); ?></td>
											<td><input type="text" id="Title" name="Title" size="30" value="<?php echo stripslashes(attribute_escape( $submission->Title )); ?>" tabindex="4" id="name" /></td>
										</tr>
									</tbody>
								</table>
								<br />
							</div>
						</div>
						<div id="postdiv" class="postarea">
							<?php
							add_filter('user_can_richedit', create_function ('$a', 'return false;') , 50);	// Disable visual editor
							the_editor(stripslashes($submission->Description), 'content', 'newcomment_author_url', false, 5);
							add_filter('user_can_richedit', create_function ('$a', 'return true;') , 50);	// Enable visual editor
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
	try {
		document.post.Name.focus();
	} catch(e) {}
	</script>
		<?php endif; ?>
	</div>
	<?php
}

/**
 *		Admin page for settings.
 *		@version 1.3
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
			$error[] = __('An API Key is required to use Akismet.', 'fvcn');
		}
		
		if (isset($_FILES['fvcn_defaultImage']) && !empty($_FILES['fvcn_defaultImage']) && !empty($_FILES['fvcn_defaultImage']['name'])) {
			if (!fvCommunityNewsCheckImageUpload($_FILES['fvcn_defaultImage'], true)) {
				$error[] = __('The image you are trying to upload is invalid.', 'fvcn');
			} else {
				$ext = explode('.', $_FILES['fvcn_defaultImage']['name']);
				$ext = strtolower( $ext[ count($ext)-1 ] );
				move_uploaded_file($_FILES['fvcn_defaultImage']['tmp_name'], ABSPATH . '/wp-fvcn-images/default.' . $ext);
				
				update_option('fvcn_defaultImage', $ext);
			}
		}
		
		$settings = array(
			'fvcn_captchaLength'			=> 'int',
			'fvcn_maxImageW'				=> 'int',
			'fvcn_maxImageH'				=> 'int',
			'fvcn_numRSSItems'				=> 'int',
			'fvcn_numSubmissions'			=> 'int',
			'fvcn_maxDescriptionLength'		=> 'int',
			'fvcn_maxTitleLength'			=> 'int',
			'fvcn_captchaEnabled'			=> 'bool',
			'fvcn_hideCaptchaLoggedIn'		=> 'bool',
			'fvcn_alwaysAdmin'				=> 'bool',
			'fvcn_previousApproved'			=> 'bool',
			'fvcn_loggedIn'					=> 'bool',
			'fvcn_mySubmissions'			=> 'bool',
			'fvcn_mailOnSubmission'			=> 'bool',
			'fvcn_mailOnModeration'			=> 'bool',
			'fvcn_akismetEnabled'			=> 'bool',
			'fvcn_rssEnabled'				=> 'bool',
			'fvcn_uploadImage'				=> 'bool',
			'fvcn_incStyle'					=> 'bool',
			'fvcn_captchaBgColor'			=> 'string',
			'fvcn_captchaLColor'			=> 'string',
			'fvcn_captchaTsColor'			=> 'string',
			'fvcn_captchaTColor'			=> 'string',
			'fvcn_titleBreaker'				=> 'string',
			'fvcn_descriptionBreaker'		=> 'string',
			'fvcn_submissionTemplate'		=> 'string',
			'fvcn_rssLocation'				=> 'string',
			'fvcn_akismetApiKey'			=> 'string',
			'fvcn_responseOversizedImage'	=> 'string',
			'fvcn_responseInvalidImage'		=> 'string',
			'fvcn_responseInvalidEmail'		=> 'string',
			'fvcn_responseEmpty'			=> 'string',
			'fvcn_responseSuccess'			=> 'string',
			'fvcn_responseInvalidCaptcha'	=> 'string',
			'fvcn_responseBumping'			=> 'string',
			'fvcn_responseLoggedIn'			=> 'string',
			'fvcn_responseFailure'			=> 'string',
			'fvcn_responseModeration'		=> 'string'
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
					update_option($setting, stripslashes( (string)$_POST[ $setting ]) );
					break;
			}
		}
		
		
		if (!$error)
			echo '<div id="message" class="updated fade"><p>' . __('Settings updated.', 'fvcn') . '</p></div>';
		else
			echo '<div class="error"><ul><li><strong>ERROR: </strong>' . implode('</li><li><strong>' . __('ERROR:', 'fvcn') . ' </strong>', $error) . '</li></ul></div>';
	}
		
	if ('0' == get_option('fvcn_uploadDir') && '1' == get_option('fvcn_uploadImage'))
		echo '<div class="error"><ul><li><strong>' . __('ERROR:', 'fvcn') . ' </strong>' . __('Failed to create image dir, please create it manualy.', 'fvcn') . '</li></ul></div>';
	?>
	<div id="tab-interface" class="wrap">
		<h2><?php _e('Community News Settings', 'fvcn'); ?></h2>
		<ul class="subsubsub">
			<li><a href="#general" rel="#general" class="tab current"><?php _e('General', 'fvcn'); ?></a> |</li>
			<li><a href="#antispam" rel="#antispam" class="tab"><?php _e('Spam Protection', 'fvcn'); ?></a> |</li>
			<li><a href="#template" rel="#template" class="tab"><?php _e('Template', 'fvcn'); ?></a> |</li>
			<li><a href="#images" rel="#images" class="tab"><?php _e('Image Uploading', 'fvcn'); ?></a> |</li>
			<li><a href="#rss" rel="#rss" class="tab"><?php _e('RSS', 'fvcn'); ?></a> |</li>
			<li><a href="#appearance" rel="#appearance" class="tab"><?php _e('Appearance', 'fvcn'); ?></a></li>
		</ul>
		<br class="clear" />
		
		<form method="post" action="" id="tabContainer" enctype="multipart/form-data">
			<?php wp_nonce_field('fvCommunityNews_changeSettings'); ?>
			
			<div id="general" class="tabdiv currentTab">
				<h3><?php _e('General Settings', 'fvcn'); ?></h3>
				<p><?php _e('General Settings', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Before a submission appears', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Before a submission appears', 'fvcn'); ?></legend>
								<label for="fvcn_alwaysAdmin">
									<input type="checkbox" name="fvcn_alwaysAdmin" id="fvcn_alwaysAdmin" value="1"<?php if (get_option('fvcn_alwaysAdmin')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('An administrator must always approve the submission.', 'fvcn'); ?></span></label>
								<br />
								<label for="fvcn_previousApproved">
									<input type="checkbox" name="fvcn_previousApproved" id="fvcn_previousApproved" value="1"<?php if (get_option('fvcn_previousApproved')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('Submission author must have a previously approved submission.', 'fvcn'); ?></span></</label>
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('E-mail me whenever', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('E-mail me whenever', 'fvcn'); ?></legend>
								<label for="fvcn_mailOnSubmission">
									<input type="checkbox" name="fvcn_mailOnSubmission" id="fvcn_mailOnSubmission" value="1"<?php if (get_option('fvcn_mailOnSubmission')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('Anyone posts a submission.', 'fvcn'); ?></span></label>
								<br />
								<label for="fvcn_mailOnModeration">
									<input type="checkbox" name="fvcn_mailOnModeration" id="fvcn_mailOnModeration" value="1"<?php if (get_option('fvcn_mailOnModeration')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('A submission is held for moderation.', 'fvcn'); ?></span></label>
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_maxTitleLength"><?php _e('Maximum Title Length', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_maxTitleLength" id="fvcn_maxTitleLength" value="<?php echo get_option('fvcn_maxTitleLength'); ?>" size="4" /> <span class="setting-description"><?php _e('Chars', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_titleBreaker"><?php _e('Break Title With', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_titleBreaker" id="fvcn_titleBreaker" value="<?php echo get_option('fvcn_titleBreaker'); ?>" size="6" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_maxDescriptionLength"><?php _e('Maximum Description Length', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_maxDescriptionLength" id="fvcn_maxDescriptionLength" value="<?php echo get_option('fvcn_maxDescriptionLength'); ?>" size="4" /> <span class="setting-description"><?php _e('Chars', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_descriptionBreaker"><?php _e('Break Description With', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_descriptionBreaker" id="fvcn_descriptionBreaker" value="<?php echo get_option('fvcn_descriptionBreaker'); ?>" size="6" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('My Submissions', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('My Submissions', 'fvcn'); ?></legend>
								<label for="fvcn_mySubmissions">
									<input type="checkbox" name="fvcn_mySubmissions" id="fvcn_mySubmissions" value="1"<?php if (get_option('fvcn_mySubmissions')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('Add a `My Submissions` page where registered users could view and add their submissions.', 'fvcn'); ?></span></label>
							</fieldset></td>
					</tr>
				</table>
			</div>
			
			<div id="antispam" class="tabdiv">
				<h3><?php _e('Spam Protection', 'fvcn'); ?></h3>
				<p><?php _e('Get rid of those damn spambots. (Some default protection is already build-in)', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Akismet', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Akismet', 'fvcn'); ?></legend>
								<label for="fvcn_akismetEnabled">
									<input type="checkbox" name="fvcn_akismetEnabled" id="fvcn_akismetEnabled" value="1"<?php if (get_option('fvcn_akismetEnabled')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('Enable Akismet spam protection.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_akismetApiKey"><?php _e('WordPress.com API Key', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_akismetApiKey" id="fvcn_akismetApiKey" value="<?php echo get_option('fvcn_akismetApiKey'); ?>" class="code" /> <span class="setting-description"><a href="http://wordpress.com/api-keys/" target="_blank"><?php _e('Get a key', 'fvcn'); ?></a> (<a href="http://faq.wordpress.com/2005/10/19/api-key/" target="_blank"><?php _e('What is this?', 'fvcn'); ?></a>)</span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Authentication', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Authentication', 'fvcn'); ?></legend>
								<label for="fvcn_loggedIn">
									<input type="checkbox" name="fvcn_loggedIn" id="fvcn_loggedIn" value="1"<?php if (get_option('fvcn_loggedIn')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('Submission author must be logged in.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Enable Captcha', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Enable a Captcha Image', 'fvcn'); ?></legend>
								<label for="fvcn_captchaEnabled">
									<input type="checkbox" name="fvcn_captchaEnabled" id="fvcn_captchaEnabled" value="1"<?php if (get_option('fvcn_captchaEnabled')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('Enable or disable the use of a captcha.', 'fvcn'); ?></span></label>
								<br />
								<label for="fvcn_hideCaptchaLoggedIn">
									<input type="checkbox" name="fvcn_hideCaptchaLoggedIn" id="fvcn_hideCaptchaLoggedIn" value="1"<?php if (get_option('fvcn_hideCaptchaLoggedIn')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('Remove captcha for users who are already logged in.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_captchaLength"><?php _e('Captcha length', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_captchaLength" id="fvcn_captchaLength" value="<?php echo get_option('fvcn_captchaLength'); ?>" size="2" /> <span class="setting-description"><?php _e('Chars', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_captchaBgColor"><?php _e('Background Color', 'fvcn'); ?></label></th>
						<td>#<input type="text" name="fvcn_captchaBgColor" id="fvcn_captchaBgColor" value="<?php echo get_option('fvcn_captchaBgColor'); ?>" size="6" class="code" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_captchaTColor"><?php _e('Text Color', 'fvcn'); ?></label></th>
						<td>#<input type="text" name="fvcn_captchaTColor" id="fvcn_captchaTColor" value="<?php echo get_option('fvcn_captchaTColor'); ?>" size="6" class="code" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_captchaTsColor"><?php _e('Textshadow Color', 'fvcn'); ?></label></th>
						<td>#<input type="text" name="fvcn_captchaTsColor" id="fvcn_captchaTsColor" value="<?php echo get_option('fvcn_captchaTsColor'); ?>" size="6" class="code" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_captchaLColor"><?php _e('Line Color', 'fvcn'); ?></label></th>
						<td>#<input type="text" name="fvcn_captchaLColor" id="fvcn_captchaLColor" value="<?php echo get_option('fvcn_captchaLColor'); ?>" size="6" class="code" /></td>
					</tr>
				</table>
			</div>
			
			<div id="template" class="tabdiv">
				<h3><?php _e('Template', 'fvcn'); ?></h3>
				<p><?php _e('These settings could be overwritten with values in your template tags.', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="fvcn_numSubmissions"><?php _e('Number of Submissions', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_numSubmissions" id="fvcn_numSubmissions" value="<?php echo get_option('fvcn_numSubmissions'); ?>" size="2" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Submission Template', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Submission Template', 'fvcn'); ?></legend>
								<p>
									<label for="fvcn_submissionTemplate"><span class="setting-description"><?php _e('The template for a single submission.<br />You can use the following tags: <strong>%submission_author%</strong>, <strong>%submission_author_email%</strong>, <strong>%submission_title%</strong>, <strong>%submission_url%</strong>, <strong>%submission_description%</strong>, <strong>%submission_date%</strong>, <strong>%submission_image%</strong>.', 'fvcn'); ?></span></label>
								</p>
								<p>
									<textarea name="fvcn_submissionTemplate" id="fvcn_submissionTemplate" cols="60" rows="10" style="width: 98%; font-size: 12px;" class="code"><?php echo stripslashes(get_option('fvcn_submissionTemplate')); ?></textarea>
							</p>
							</fieldset></td>
					</tr>
				</table>
			</div>
			
			<div id="images" class="tabdiv">
				<h3><?php _e('Images', 'fvcn'); ?></h3>
				<p><?php _e('It is possible to allow people to upload an image together with their submission.', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Enable Image Uploading', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Enable Image Uploading', 'fvcn'); ?></legend>
								<label for="fvcn_uploadImage">
									<input type="checkbox" name="fvcn_uploadImage" id="fvcn_uploadImage" value="1"<?php if (get_option('fvcn_uploadImage')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('Allow people to upload an image.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_defaultImage"><?php _e('Default Image', 'fvcn'); ?></label></th>
						<td>
							<input type="file" name="fvcn_defaultImage" id="fvcn_defaultImage" value="" /> <span class="setting-description"><?php _e('The image that will be used if no image is uploaded.', 'fvcn'); ?></span><br /><br />
							<?php $image = ('default'==get_option('fvcn_defaultImage')?WP_PLUGIN_URL.'/fv-community-news/images/default.png':get_option('home').'/wp-fvcn-images/default.'.get_option('fvcn_defaultImage')); ?>
							<img src="<?php echo $image; ?>" alt="" /><br /><small><?php _e('Current default image.', 'fvcn'); ?></small>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Max Image Size', 'fvcn'); ?></th>
						<td>
							<span style="float:left;width:45px;padding:4px 0"><?php _e('Width:', 'fvcn'); ?></span><input type="text" name="fvcn_maxImageW" id="fvcn_maxImageW" value="<?php echo get_option('fvcn_maxImageW'); ?>" size="5" /> <span class="setting-description"><?php _e('pixels', 'fvcn'); ?></span><br class="clear" />
							<span style="float:left;width:45px;padding:4px 0"><?php _e('Height:', 'fvcn'); ?></span><input type="text" name="fvcn_maxImageH" id="fvcn_maxImageH" value="<?php echo get_option('fvcn_maxImageH'); ?>" size="5" /> <span class="setting-description"><?php _e('pixels', 'fvcn'); ?></span><br class="clear" />&nbsp; &nbsp;<span class="setting-description"><?php _e('(0 = Unlimited)', 'fvcn'); ?></span>
						</td>
					</tr>
				</table>
			</div>
			
			<div id="rss" class="tabdiv">
				<h3><?php _e('RSS', 'fvcn'); ?></h3>
				<p><?php _e('Configure your Community News RSS 2.0 feed.', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Enable RSS Feed', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Enable the RSS Feed', 'fvcn'); ?></legend>
								<label for="fvcn_rssEnabled">
									<input type="checkbox" name="fvcn_rssEnabled" id="fvcn_rssEnabled" value="1"<?php if (get_option('fvcn_rssEnabled')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('Enable or disable the RSS 2.0 Feed.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_numRSSItems"><?php _e('Number of RSS Items', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_numRSSItems" id="fvcn_numRSSItems" value="<?php echo get_option('fvcn_numRSSItems'); ?>" size="3" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_rssLocation"><?php _e('RSS Location', 'fvcn'); ?></label></th>
						<td><?php
						if ($wp_rewrite->using_permalinks())
							echo get_option('home') . '/' . str_replace('feed/%feed%', '', $wp_rewrite->get_feed_permastruct());
						else
							echo get_option('home') . '/?feed=';
						?><input type="text" name="fvcn_rssLocation" id="fvcn_rssLocation" value="<?php echo get_option('fvcn_rssLocation'); ?>" style="padding:0" /></td>
					</tr>
				</table>
			</div>
			
			<div id="appearance" class="tabdiv">
				<h3><?php _e('Appearance', 'fvcn'); ?></h3>
				<p><?php _e('Change the look/responses of this plugin.', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Include StyleSheet', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Include StyleSheet', 'fvcn'); ?></legend>
								<label for="fvcn_incStyle">
									<input type="checkbox" name="fvcn_incStyle" id="fvcn_incStyle" value="1"<?php if (get_option('fvcn_incStyle')) echo ' checked="checked"'; ?> />
									<span class="setting-description"><?php _e('Include a simple stylesheet to change the look of this plugin.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_responseSuccess"><?php _e('Successful Posted', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_responseSuccess" id="fvcn_responseSuccess" value="<?php echo get_option('fvcn_responseSuccess'); ?>" style="width:98%" /> <br /><span class="setting-description"><?php _e('The message displayed when a submission is added successfull.', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_responseEmpty"><?php _e('Empty Fields', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_responseEmpty" id="fvcn_responseEmpty" value="<?php echo get_option('fvcn_responseEmpty'); ?>" style="width:98%" /> <br /><span class="setting-description"><?php _e('The message displayed when not all required fields are filled in.', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_responseInvalidEmail"><?php _e('Invalid Email', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_responseInvalidEmail" id="fvcn_responseInvalidEmail" value="<?php echo get_option('fvcn_responseInvalidEmail'); ?>" style="width:98%" /> <br /><span class="setting-description"><?php _e('The message displayed when an invalid email is filled in.', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_responseInvalidImage"><?php _e('Invalid Image', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_responseInvalidImage" id="fvcn_responseInvalidImage" value="<?php echo get_option('fvcn_responseInvalidImage'); ?>" style="width:98%" /> <br /><span class="setting-description"><?php _e('The message displayed when an invalid (malicious) image file is uploaded.', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_responseOversizedImage"><?php _e('Oversized Image', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_responseOversizedImage" id="fvcn_responseOversizedImage" value="<?php echo get_option('fvcn_responseOversizedImage'); ?>" style="width:98%" /> <br /><span class="setting-description"><?php _e('The message displayed when an image is uploaded witch is too big.', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_responseInvalidCaptcha"><?php _e('Invalid Captcha', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_responseInvalidCaptcha" id="fvcn_responseInvalidCaptcha" value="<?php echo get_option('fvcn_responseInvalidCaptcha'); ?>" style="width:98%" /> <br /><span class="setting-description"><?php _e('The message displayed when an invalid captcha value is entered.', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_responseBumping"><?php _e('Bumping', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_responseBumping" id="fvcn_responseBumping" value="<?php echo get_option('fvcn_responseBumping'); ?>" style="width:98%" /> <br /><span class="setting-description"><?php _e('The message displayed when more then one submission is added within 2 minutes.', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_responseLoggedIn"><?php _e('Required Login', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_responseLoggedIn" id="fvcn_responseLoggedIn" value="<?php echo get_option('fvcn_responseLoggedIn'); ?>" style="width:98%" /> <br /><span class="setting-description"><?php _e('The message displayed when an user is required to log in.', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_responseFailure"><?php _e('Posting Failure', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_responseFailure" id="fvcn_responseFailure" value="<?php echo get_option('fvcn_responseFailure'); ?>" style="width:98%" /> <br /><span class="setting-description"><?php _e('The message displayed when an error occured while adding a submission.', 'fvcn'); ?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_responseModeration"><?php _e('Awaiting Moderation', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_responseModeration" id="fvcn_responseModeration" value="<?php echo get_option('fvcn_responseModeration'); ?>" style="width:98%" /> <br /><span class="setting-description"><?php _e('The message displayed when a submission is awaiting moderation.', 'fvcn'); ?></span></td>
					</tr>
				</table>
			</div>
			<p class="submit">
				<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes', 'fvcn'); ?>" />
			</p>
		</form>
	</div>
	<?php
}

/**
 *		Uninstall the FV Community News Plugin
 *		@since 1.3
 *		@version 1.0
 */
function fvCommunityNewsUninstall() {
	if (!current_user_can('manage_options'))
		exit;
	
	global $wpdb;
	
	if ('POST' == $_SERVER['REQUEST_METHOD'] && check_admin_referer('fvCommunityNews_uninstallPlugin')) {
		if ($_POST['fvcn_confirmUninstall'] != $_POST['fvcn_confirmCode']) {
			echo '<div class="error"><ul><li><strong>' . __('ERROR:', 'fvcn') . ' </strong>' . __('Wrong confirm code, please try again.', 'fvcn') . '</li></ul></div>';
		} else {
			if (!empty($_POST['fvcn_removeData'])) {
				// Remove DB Table
				$wpdb->query("DROP TABLE " . get_option('fvcn_dbname'));
				// Remove Images+Directory
				fvCommunityNewsRemoveDirectory(ABSPATH . 'wp-fvcn-images');
			}
			if (!empty($_POST['fvcn_removeSettings'])) {
				// Remove all settings used by this plugin
				delete_option('fvcn_version');
				delete_option('fvcn_dbname');
				delete_option('fvcn_dbversion');
				delete_option('fvcn_captchaEnabled');
				delete_option('fvcn_hideCaptchaLoggedIn');
				delete_option('fvcn_captchaLength');
				delete_option('fvcn_captchaBgColor');
				delete_option('fvcn_captchaLColor');
				delete_option('fvcn_captchaTsColor');
				delete_option('fvcn_captchaTColor');
				delete_option('fvcn_alwaysAdmin');
				delete_option('fvcn_previousApproved');
				delete_option('fvcn_mailOnSubmission');
				delete_option('fvcn_mailOnModeration');
				delete_option('fvcn_maxTitleLength');
				delete_option('fvcn_titleBreaker');
				delete_option('fvcn_maxDescriptionLength');
				delete_option('fvcn_descriptionBreaker');
				delete_option('fvcn_numSubmissions');
				delete_option('fvcn_submissionTemplate');
				delete_option('fvcn_formTitle');
				delete_option('fvcn_submissionsTitle');
				delete_option('fvcn_rssEnabled');
				delete_option('fvcn_numRSSItems');
				delete_option('fvcn_rssLocation');
				delete_option('fvcn_loggedIn');
				delete_option('fvcn_uploadImage');
				delete_option('fvcn_maxImageW');
				delete_option('fvcn_maxImageH');
				delete_option('fvcn_mySubmissions');
				delete_option('fvcn_akismetEnabled');
				delete_option('fvcn_defaultImage');
				delete_option('fvcn_incStyle');
				delete_option('fvcn_responseOversizedImage');
				delete_option('fvcn_responseInvalidImage');
				delete_option('fvcn_responseInvalidEmail');
				delete_option('fvcn_responseEmpty');
				delete_option('fvcn_responseSuccess');
				delete_option('fvcn_responseInvalidCaptcha');
				delete_option('fvcn_responseBumping');
				delete_option('fvcn_responseLoggedIn');
				delete_option('fvcn_responseFailure');
				delete_option('fvcn_responseModeration');
				delete_option('fvcn_akismetApiKey');
				delete_option('fvcn_uplodeleteir');
			}
			$plugin = 'fv-community-news/fvCommunityNews.php';
			deactivate_plugins($plugin);
			update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));
			die('<meta http-equiv="refresh" content="0;URL=plugins.php?deactivate=true" />');
		}
	}
	
	if ('0' == get_option('fvcn_uploadDir') && '1' == get_option('fvcn_uploadImage'))
			echo '<div class="error"><ul><li><strong>' . __('ERROR:', 'fvcn') . ' </strong>' . __('Failed to create image dir, please create it manualy.', 'fvcn') . '</li></ul></div>';
	
	?>
	<div class="wrap">
		<h2><?php _e('Community News Uninstall', 'fvcn'); ?></h2>
		<form method="post" action="admin.php?page=fvCommunityNewsUninstall">
			<?php wp_nonce_field('fvCommunityNews_uninstallPlugin'); ?>
			<p><?php _e('After the uninstall, you should manually remove the plugin files.', 'fvcn'); ?></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Remove Settings', 'fvcn'); ?></th>
					<td><fieldset>
							<legend class="hidden"><?php _e('Remove Settings', 'fvcn'); ?></legend>
							<label for="fvcn_removeSettings">
								<input type="checkbox" name="fvcn_removeSettings" id="fvcn_removeSettings" value="1" checked="checked" />
								<span class="setting-description"><?php _e('Remove the plugin settings.', 'fvcn'); ?></span></label>
							<br />
						</fieldset></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Remove Data', 'fvcn'); ?></th>
					<td><fieldset>
							<legend class="hidden"><?php _e('Remove Data', 'fvcn'); ?></legend>
							<label for="fvcn_removeData">
								<input type="checkbox" name="fvcn_removeData" id="fvcn_removeData" value="1" />
								<span class="setting-description"><?php _e('Remove the plugin data (Submissions + Pictures).', 'fvcn'); ?></span></label>
							<br />
						</fieldset></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="fvcn_confirmUninstall"><?php _e('Confirm', 'fvcn'); ?></label></th>
					<td><input type="text" name="fvcn_confirmUninstall" id="fvcn_confirmUninstall" value="" />
					<?php $code = mt_rand(111, 999); ?>
					<span class="setting-description"><strong><?php _e('Code:', 'fvcn'); echo ' ' . $code; ?></strong>
					<?php _e('Please type the code to confirm your uninstall.', 'fvcn'); ?></span>
					<input type="hidden" name="fvcn_confirmCode" value="<?php echo $code; ?>" /></td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" name="Submit" value="<?php _e('Uninstall', 'fvcn'); ?>" />
			</p>
		</form>
	</div>
	<?php
}

?>