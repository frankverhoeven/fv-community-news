<?php
/**
 *		Plugin Name:		FV Community News
 *		Plugin URI:			http://www.frank-verhoeven.com/wordpress-plugin-fv-community-news/
 *		Description:		Let visiters of your site post their articles on your site.
 *		Version:			1.1.1
 *		Author:				Frank Verhoeven
 *		Author URI:			http://www.frank-verhoeven.com/
 *		@copyright			Copyright (c) 2008, Frank Verhoeven
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
$fvCommunityNewsVersion = '1.1';

/**
 *		Initialize the application
 *		@version 1.1
 */
function fvCommunityNewsInit() {
	global $wp_registered_widgets, $fvCommunityNewsVersion;
	
	if (!headers_sent() && !session_id())
		session_start();
	
	if (!get_option('fvcn_version'))
		fvCommunityNewsInstall();
	if ($fvCommunityNewsVersion > get_option('fvcn_version'))
		fvCommunityNewsUpdate();
	
	add_action('wp_head', 'fvCommunityNewsHead', 30);
	add_action('admin_head', 'fvCommunityNewsAdminHead');
	add_action('admin_menu', 'fvCommunityNewsAddAdmin');
	
	// Add RSS Feed if enabled
	if (get_option('fvcn_rssEnabled')) {
		$location = (get_option('fvcn_rssLocation')?get_option('fvcn_rssLocation'):'community-news.rss');
		update_option('fvcn_rssHook', add_feed($location, 'fvCommunityNewsRSSFeed') );
	}
	
	// Add widgets
	if (function_exists('register_sidebar_widget')) {
		register_sidebar_widget('Community News Form', 'fvCommunityNewsFormWidget');
		$wp_registered_widgets[sanitize_title('Community News Form')]['description'] = 'The default submissions form.';
		register_widget_control('Community News Form', 'fvCommunityNewsFormWidgetControl');
		
		register_sidebar_widget('Community News Submissions', 'fvCommunityNewsGetSubmissionsWidget');
		$wp_registered_widgets[sanitize_title('Community News Submissions')]['description'] = 'A list of submissions.';
		register_widget_control('Community News Submissions', 'fvCommunityNewsGetSubmissionsWidgetControl');
	}
	
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
 *		@version 1.0
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
 *		@version 1.0
 */
function fvCommunityNewsBreaker($string, $maxLength, $breaker='&hellip;') {
	return strlen($string)>$maxLength ? substr($string, 0, $maxLength).$breaker : $string; 
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
 *		@version 1.0.1
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
 *		@version 1.1
 */
function fvCommunityNewsHead() {
	global $wp_rewrite;
	$dir = get_option('home') . '/wp-content/plugins/fv-community-news/javascript/';
	
	echo "\n\t\t" . '<script type="text/javascript" src="' . $dir . 'prototype.js"></script>' . "\n";
	echo "\t\t" . '<script type="text/javascript" src="' . $dir . 'scriptaculous.js"></script>' . "\n";
	echo "\t\t" . '<script type="text/javascript" src="' . $dir . 'fvCommunityNews.js"></script>' . "\n";
	if (get_option('fvcn_rssEnabled')) {
		if ($wp_rewrite->using_permalinks())
			$location = get_option('home') . '/' . str_replace('%feed%', '', $wp_rewrite->get_feed_permastruct());
		else
			$location = get_option('home') . '/?feed=';
		$location .= get_option('fvcn_rssLocation');
		
		echo "\t\t" . '<link rel="alternate" type="application/rss+xml" title="' . get_option('blogname') . ' Community News RSS Feed" href="' . $location . '" />' . "\n";
	}
	echo "\t\t" . '<meta name="Community-News-Creator" content="FV Community News" />' . "\n\n";
}

/**
 *		Install the application.
 *		@version 1.1
 */
function fvCommunityNewsInstall() {
	global $wpdb, $fvCommunityNewsVersion;
	
	add_option('fvcn_version', $fvCommunityNewsVersion);
	
	$tableName = $wpdb->prefix . 'fv_community_news';
	add_option('fvcn_dbname', $tableName);
	add_option('fvcn_dbversion', '1.0');
	
	if ($wpdb->get_var("SHOW TABLES LIKE '" . $tableName . "'") != $tableName) {
		$sql = "CREATE TABLE " . $tableName . " (
					Id INT( 255 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					Name VARCHAR( 50 ) NOT NULL ,
					Email VARCHAR( 75 ) NOT NULL ,
					Title VARCHAR( 150 ) NOT NULL ,
					Location VARCHAR( 250 ) NOT NULL ,
					Description MEDIUMTEXT NOT NULL ,
					Date DATETIME NOT NULL ,
					Ip VARCHAR( 25 ) NOT NULL ,
					Host VARCHAR( 150 ) NOT NULL,
					Approved SMALLINT( 1 ) NOT NULL
				);";
		
		if (file_exists(ABSPATH . 'wp-admin/includes/upgrade.php'))
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		elseif (file_exists(ABSPATH . 'wp-admin/upgrade.php'))
			require_once ABSPATH . 'wp-admin/upgrade.php';
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
}

/**
 *		Update the application.
 *		@since 1.1
 *		@version 1.0
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
}

/**
 *		A submission is posted and handled here.
 *		@return bool True if the submission is successfull posted, false otherwise.
 *		@version 1.1
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
			(empty($_POST['fvCommunityNewsLocation'])		|| $_POST['fvCommunityNewsLocation'] == $fvCommunityNewsFieldValues['fvCommunityNewsLocation']) ||
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
			$approved = 0;
		}
	} elseif (get_option('fvcn_previousApproved') && !($wpdb->query( "SELECT Id FROM " . get_option('fvcn_dbname') . " WHERE Email = '" . $wpdb->escape($email) ."' AND Approved = '1'") > 0)) {
		$fvCommunityNewsAwaitingModeration = true;
		if (get_option('fvcn_mailOnModeration')) {
			$modmail = true;
			$approved = 0;
		}
	} else {
		$approved = 1;
	}
	
	if (get_option('fvcn_mailOnSubmission'))
		$postmail = true;
	
	if ($postmail) {
		wp_mail(
			get_option('admin_email'),
			'[' . get_option('blogname') . '] Submission: "' . $title . '"',
			'New submission.' . "\n" .
			'Author:' . $name . ' (Ip: ' . $ip . ")\n" .
			'E-mail: ' . $email . "\n" .
			'URL: ' . $location . "\n" .
			'Whois: http://ws.arin.net/cgi-bin/whois.pl?queryinput=' . $ip . "\n" .
			'Description:' . "\n" . $description . "\n\n" .
			'Moderation Page: ' . get_option('home') . '/wp-admin/admin.php?page=fv-community-news/fvCommunityNews.php&submission_status=moderation'
			);
	} elseif ($modmail) {
		wp_mail(
			get_option('admin_email'),
			'[' . get_option('blogname') . '] Please Moderate: "' . $title . '"',
			'A new submission is waiting for your approval.' . "\n" .
			'Author:' . $name . ' (Ip: ' . $ip . ")\n" .
			'E-mail: ' . $email . "\n" .
			'URL: ' . $location . "\n" .
			'Whois: http://ws.arin.net/cgi-bin/whois.pl?queryinput=' . $ip . "\n" .
			'Description:' . "\n" . $description . "\n\n" .
			'Moderation Page: ' . get_option('home') . '/wp-admin/admin.php?page=fv-community-news/fvCommunityNews.php&submission_status=moderation'
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
				'" . $wpdb->escape($approved) . "'
			)";
	$result = $wpdb->query($sql);
	if (!$result) {
		$fvNewPosterSubmitError = 'Unable to add your post, please try again later.';
		return false;
	}
	
	$_SESSION['fvCommunityNewsLastPost'] = (current_time('timestamp')+120);
	
	return true;
}

/**
 *		Gives the errors (if any) from the posted submission.
 *		@return bool False if no errors occured, otherwise a string containing the occured error.
 *		@version 1.0
 */
function fvCommunityNewsSubmitError() {
	global $fvCommunityNewsSubmitError;
	
	if ($fvCommunityNewsSubmitError)
		return $fvCommunityNewsSubmitError;
	return false;
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
 *		@version 1.0
 */
function fvCommunityNewsGetSubmissions($number=5, $format=false) {
	global $wpdb;
	
	$format = $format?$format:'<li><h3><a href="%submission_url%" title="%submission_title%">%submission_title%</a></h3><small>%submission_date%</small><br />%submission_description%</li>';
	
	if (get_option('fvcn_numSubmissions'))
		$number = get_option('fvcn_numSubmissions');
	if (get_option('fvcn_submissionTemplate'))
		$format = stripslashes(get_option('fvcn_submissionTemplate'));
	
	$sql = "SELECT
				Name,
				Email,
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
	
	$posts = $wpdb->get_results($sql);
	
	if (empty($posts))
		return '<!-- No posts found. //-->';
	
	$newsPosts = '<ul class="fvCommunityNewsList">';
	foreach ($posts as $post) {
		$newsPosts .= $format . "\n";
		
		$newsPosts = str_replace('%submission_author%', stripslashes(apply_filters('comment_author', $post->Name)), $newsPosts);
		$newsPosts = str_replace('%submission_author_email%', stripslashes(apply_filters('comment_author_email', $post->Email)), $newsPosts);
		$newsPosts = str_replace('%submission_title%', fvCommunityNewsBreaker(stripslashes(apply_filters('comment_author', $post->Title)), get_option('fvcn_maxTitleLength'), get_option('fvcn_titleBreaker')), $newsPosts);
		$newsPosts = str_replace('%submission_url%', stripslashes(apply_filters('comment_author_url', $post->Location)), $newsPosts);
		$newsPosts = str_replace('%submission_description%', fvCommunityNewsBreaker(stripslashes(apply_filters('comment_text', $post->Description)), get_option('fvcn_maxDescriptionLength'), get_option('fvcn_descriptionBreaker')), $newsPosts);
		$newsPosts = str_replace('%submission_date%', stripslashes(apply_filters('comment_date', mysql2date(get_option('date_format'), $post->Date) )), $newsPosts);
	}
	$newsPosts .= '</ul>';
	
	return $newsPosts;
}

/**
 *		Create a RSS 2.0 feed from the latest subissions.
 *		@since 1.1
 *		@version 1.0
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
	
	echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?>';
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
				<description><?php echo fvCommunityNewsBreaker(stripslashes(apply_filters('comment_text', $item->Description)), get_option('fvcn_maxDescriptionLength'), get_option('fvcn_descriptionBreaker')) ?></description>
				<content:encoded><![CDATA[<?php echo stripslashes(apply_filters('comment_text', $item->Description)); ?>]]></content:encoded>
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
 *		For people who are using widgets.
 *		@param array $args Options for the widget.
 *		@version 1.0
 */
function fvCommunityNewsFormWidget($args) {
	extract($args);
	
	echo $before_widget;
	echo $before_title . get_option('fvcn_formTitle') . $after_title;
	
	fvCommunityNewsForm();
	
	echo $after_widget;
}

/**
 *		Some settings for the form widget.
 *		@version 1.0
 */
function fvCommunityNewsFormWidgetControl() {
	if (!empty($_POST['fvcn_formTitle']))
		update_option('fvcn_formTitle', strip_tags($_POST['fvcn_formTitle']));
	
	?>
	<p>
		<label for="fvcn_formTitle">Title
		<input type="text" id="fvcn_formTitle" name="fvcn_formTitle" value="<?php echo get_option('fvcn_formTitle'); ?>" class="widefat" />
		</label>
	</p>
	<?php
}

/**
 *		For people who are using widgets.
 *		@param array $args Options for the widget.
 *		@version 1.0
 */
function fvCommunityNewsGetSubmissionsWidget($args) {
	extract($args);
	
	echo $before_widget;
	echo $before_title . get_option('fvcn_submissionsTitle') . $after_title;
	
	echo fvCommunityNewsGetSubmissions();
	
	echo $after_widget;
}

/**
 *		Some settings for the submissions widget.
 *		@version 1.0
 */
function fvCommunityNewsGetSubmissionsWidgetControl() {
	if (!empty($_POST['fvcn_submissionsTitle']))
		update_option('fvcn_submissionsTitle', strip_tags($_POST['fvcn_submissionsTitle']));
	
	?>
	<p>
		<label for="fvcn_submissionsTitle">Title
		<input type="text" id="fvcn_submissionsTitle" name="fvcn_submissionsTitle" value="<?php echo get_option('fvcn_submissionsTitle'); ?>" class="widefat" />
		</label>
	</p>
	<?php
}

/**
 *		Add some admin pages to the wp-admin.
 *		@version 1.0
 */
function fvCommunityNewsAddAdmin() {
	add_menu_page('Manage submissions', 'Community News', 'level_7', __FILE__, 'fvCommunityNewsSubmissions');
	add_submenu_page(__FILE__, 'Community News Settings', 'Settings', 'level_7', 'fvCommunityNewsSettings', 'fvCommunityNewsSettings');
}

/**
 *		Get a list of submissions for the moderation panel.
 *		@param string $status The current status of the submissions.
 *		@param int $start The start of submissions.
 *		@param int $num The number of submissions per page.
 *		@return array The submissions, Total amount of submissions
 *		@version 1.0
 *		@since 1.1
 */
function fvCommunityNewsGetSubmissionsList( $status = '', $start, $num ) {
	global $wpdb;

	$start = abs( (int) $start );
	$num = (int) $num;

	if ( 'moderation' == $status )
		$approved = "Approved = '0'";
	elseif ( 'approved' == $status )
		$approved = "Approved = '1'";
	else
		$approved = "( Approved = '0' OR Approved = '1' )";

	$submissions = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM " . get_option('fvcn_dbname') . " WHERE $approved ORDER BY Date DESC LIMIT $start, $num");

	$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );

	return array($submissions, $total);
}

/**
 *		Add some javascript to the admin header.
 */
function fvCommunityNewsAdminHead() {
	$dir = get_option('home') . '/wp-content/plugins/fv-community-news/javascript/';
	if (isset($_GET['page']) && strstr($_GET['page'], 'fvCommunityNews'))
		echo '<script type="text/javascript" src="' . $dir . 'fvCommunityNewsAdmin.js"></script>' . "\n";
}

/**
 *		Admin page for managing submissions.
 *		@version 1.1
 */
function fvCommunityNewsSubmissions() {
	global $wpdb;
	
	if (!current_user_can('moderate_comments'))
		exit;
	
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
	
	
	?>
<div class="wrap">
		<!--<h2>Manage Submissions</h2>-->
		<ul class="subsubsub">
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'all', $_SERVER['REQUEST_URI'])) ?>" <?php if ('all' == $submissionStatus) echo 'class="current"' ?>>Show All</a> |</li>
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'moderation', $_SERVER['REQUEST_URI'])) ?>" <?php if ('moderation' == $submissionStatus) echo 'class="current"' ?>>Awaiting Moderation <?php echo '(' . $wpdb->num_rows . ')'; ?></a> |</li>
			<li><a href="<?php echo clean_url(add_query_arg('submission_status', 'approved', $_SERVER['REQUEST_URI'])) ?>" <?php if ('approved' == $submissionStatus) echo 'class="current"' ?>>Approved</a></li>
		</ul>
		
		
		<?php
		$submissionPerPage = 10;
		
		if (isset($_GET['apage']))
			$page = abs( (int)$_GET['apage'] );
		else
			$page = 1;
		
		$start = ($page - 1) * $submissionPerPage;
		
		list($_submissions, $total) = fvCommunityNewsGetSubmissionsList( $submissionStatus, $start, $submissionPerPage + 5 ); // Grab a few extra
		
		$submissions = array_slice($_submissions, 0, $submissionPerPage);
		$extra_comments = array_slice($_submissions, $submissionPerPage);
		
		$pageLinks = paginate_links( array(
			'base' => add_query_arg( 'apage', '%#%' ),
			'format' => '',
			'total' => ceil($total / $submissionPerPage),
			'current' => $page
		));
		

		
		?>
		
		
		<form id="comments-form" action="" method="post">
			<div class="tablenav">
				<?php
				if ( $pageLinks )
					echo '<div class="tablenav-pages">' . $pageLinks . '</div>';
				?>
				<div class="alignleft">
					<input type="submit" name="submission-approve" id="submission-approve" value="Approve" class="button-secondary" />
					<input type="submit" name="submission-unapprove" id="submission-unapprove" value="Unapprove" class="button-secondary" />
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
						<th scope="col" id="date" class="manage-column column-date" style="min-width: 120px;">Submitted</th>
					</tr>
				</thead>
			<tbody id="the-comment-list" class="list:comment">
			<?php
			foreach ($submissions as $post) {
				echo '<tr id="submission-' . $post->Id . '" class="' . ('0' == $post->Approved?'unapproved':'') . '">' . "\n";
				echo ' <th scope="row" class="check-column"><input type="checkbox" name="submissions[]" value=' . $post->Id . ' /></th>' . "\n";
				echo ' <td class="comment column-comment"><strong><a href="' . stripslashes(apply_filters('comment_author_url', $post->Location)) . '">' . stripslashes(apply_filters('get_comment_author', $post->Title)) . '</a></strong><br />';
				echo trim( stripslashes(apply_filters('comment_text', $post->Description)) );
				
				/*echo '<span class="approve"><a href="';
				echo clean_url( wp_nonce_url('admin.php?page=fv-community-news/fvCommunityNews.php&amp;submissions=' . $post->Id . '&amp;submission-approve=true', 'fvCommunityNews_moderateSubmissions') );
				echo '" title="Approve this submission">Approve</a> | </span>';
				
				echo '<span class="unapprove"><a href="';
				echo clean_url( wp_nonce_url('admin.php?page=fv-community-news/fvCommunityNews.php&amp;submissions=' . $post->Id . '&amp;submission-unapprove=true', 'fvCommunityNews_moderateSubmissions') );
				echo '" title="Unapprove this submission">Unapprove</a> | </span>';
				
				echo '<span class="delete"><a href="';
				echo clean_url( wp_nonce_url('admin.php?page=fv-community-news/fvCommunityNews.php&amp;submissions=' . $post->Id . '&amp;submission-delete=true', 'fvCommunityNews_moderateSubmissions') );
				echo '" title="Delete this submission">Delete</a></span>';*/
				
				echo '</td>' . "\n";
				echo ' <td class="author column-author"><strong>' . get_avatar($post->Email, 32) . ' ' . stripslashes(apply_filters('get_comment_author', $post->Name)) . '</strong><br />';
				echo '<a href="mailto:' . stripslashes(apply_filters('get_comment_author_email', $post->Email)) . '">' . stripslashes(apply_filters('get_comment_author_email', $post->Email)) . '</a><br />';
				echo $post->Ip . '</td>' . "\n";
				
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
					<input type="submit" name="submission-delete" value="Delete" class="button-secondary delete" />
				</div>
				<br class="clear" />
			</div>
		</form>
	</div>
	<?php
}

/**
 *		Admin page for settings.
 *		@version 1.1
 */
function fvCommunityNewsSettings() {
	if (!current_user_can('moderate_comments'))
		exit;
	
	global $wp_rewrite;
	
	if ('POST' == $_SERVER['REQUEST_METHOD'] && check_admin_referer('fvCommunityNews_changeSettings')) {
		remove_action( get_option('fvcn_rssHook'), 'fvCommunityNewsRSSFeed', 10, 1);
		
		update_option('fvcn_captchaEnabled', $_POST['fvcn_captchaEnabled']);
		update_option('fvcn_hideCaptchaLoggedIn', $_POST['fvcn_hideCaptchaLoggedIn']);
		update_option('fvcn_captchaLength', $_POST['fvcn_captchaLength']);
		update_option('fvcn_captchaBgColor', $_POST['fvcn_captchaBgColor']);
		update_option('fvcn_captchaLColor', $_POST['fvcn_captchaLColor']);
		update_option('fvcn_captchaTsColor', $_POST['fvcn_captchaTsColor']);
		update_option('fvcn_captchaTColor', $_POST['fvcn_captchaTColor']);
		update_option('fvcn_alwaysAdmin', $_POST['fvcn_alwaysAdmin']);
		update_option('fvcn_previousApproved', $_POST['fvcn_previousApproved']);
		update_option('fvcn_loggedIn', $_POST['fvcn_loggedIn']);
		update_option('fvcn_mailOnSubmission', $_POST['fvcn_mailOnSubmission']);
		update_option('fvcn_mailOnModeration', $_POST['fvcn_mailOnModeration']);
		update_option('fvcn_maxTitleLength', $_POST['fvcn_maxTitleLength']);
		update_option('fvcn_titleBreaker', $_POST['fvcn_titleBreaker']);
		update_option('fvcn_maxDescriptionLength', $_POST['fvcn_maxDescriptionLength']);
		update_option('fvcn_descriptionBreaker', $_POST['fvcn_descriptionBreaker']);
		update_option('fvcn_numSubmissions', $_POST['fvcn_numSubmissions']);
		update_option('fvcn_submissionTemplate', $_POST['fvcn_submissionTemplate']);
		update_option('fvcn_rssEnabled', $_POST['fvcn_rssEnabled']);
		update_option('fvcn_numRSSItems', $_POST['fvcn_numRSSItems']);
		update_option('fvcn_rssLocation', $_POST['fvcn_rssLocation']);
		
		echo '<div id="message" class="updated fade"><p>Settings updated.</p></div>';
	}
	?>
	<div class="wrap">
		<!--<h2>Settings</h2>-->
		<form method="post" action="">
			<?php wp_nonce_field('fvCommunityNews_changeSettings'); ?>
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
							<br />
							<label for="fvcn_loggedIn">
								<input type="checkbox" name="fvcn_loggedIn" id="fvcn_loggedIn" value="1"<?php if (get_option('fvcn_loggedIn')) echo ' checked="checked"'; ?> />
								Submission author must be logged in.</label>
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
			</table>
			
			<h3>Captcha</h3>
			<p>A captcha is an image with text witch users must type for authentication.</p>
			<table class="form-table">
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
			
			<h3>Template</h3>
			<p>These settings will override settings used in the template tags.</p>
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
						echo get_option('home') . '/' . str_replace('%feed%', '', $wp_rewrite->get_feed_permastruct());
					else
						echo get_option('home') . '/?feed=';
					?><input type="text" name="fvcn_rssLocation" id="fvcn_rssLocation" value="<?php echo get_option('fvcn_rssLocation'); ?>" /></td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="Submit" value="Save Changes" />
			</p>
		</form>
	</div>
	<?php
}

?>