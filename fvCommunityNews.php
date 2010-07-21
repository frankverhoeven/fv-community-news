<?php
/**
 *		Plugin Name:		FV Community News
 *		Plugin URI:			http://www.frank-verhoeven.com/wordpress-plugin-fv-community-news/
 *		Description:		Let visiters of your site post their articles on your site. Like this plugin? Please consider <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=SB62B7H867Y4C&lc=US&item_name=Frank%20Verhoeven&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted">making a small donation</a>.
 *		Version:			1.4
 *		Author:				Frank Verhoeven
 *		Author URI:			http://www.frank-verhoeven.com/
 *		
 *		@package			FV Community News
 *		@version			1.4
 *		@author				Frank Verhoeven
 *		@copyright			Coyright (c) 2010, Frank Verhoeven
 */


function fvCommunityNewsInit() {
	global $wpdb, $wp_rewrite;
	
	define('FVCN_ROOTDIR', realpath(dirname(__FILE__)));
	define('FVCN_PLUGINDIR', str_replace(realpath(dirname(__FILE__) . '/../'), '', FVCN_ROOTDIR));
	define('FVCN_PLUGINBASENAME', plugin_basename(__FILE__));
	
	require_once FVCN_ROOTDIR . '/Library/AutoLoader.php';
	$autoLoader = new fvCommunityNewsAutoloader(FVCN_ROOTDIR);
	
	spl_autoload_register(array($autoLoader, 'autoLoad'));
	
	$reg = fvCommunityNewsRegistry::getInstance();
	$reg->wpdb = $wpdb;
	$reg->wp_rewrite = $wp_rewrite;
	unset($reg);
	
	try {
		$bootstrap = new fvCommunityNewsBootstrap();
	} catch (Exception $e) {
		if (WP_DEBUG) {
			wp_die(
				'<strong>Fv Community News Error</strong><br /><br /><br />' .
				'<strong>Caught Exception:</strong> ' . $e->getMessage() . '<br /><br />' .
				'<strong>File: </strong>' . $e->getFile() . '<br /><br />' .
				'<strong>Line: </strong>' . $e->getLine() . '<br /><br />' .
				'<strong>Stack Trace</strong><pre style="font-size: 10px">' . $e->getTraceAsString() . '</pre>'
			);
		}
	}
}

// Hook the plugin into WordPress
add_action('init', 'fvCommunityNewsInit');








function fvcn_form() {
	$form = new fvCommunityNewsForm_AddSubmission();
	$form->render();
}

function fvCommunityNewsForm() {
	trigger_error('The function "fvCommunityNewsForm()" is deprecated, use "fvcn_form()" instead!', E_USER_NOTICE);
	fvcn_form();
}


function fvcn_list_submissions() {
	$list = new fvCommunityNewsSubmissionsList();
	$list->render();
}

function fvCommunityNewsGetSubmissions() {
	trigger_error('The function "fvCommunityNewsGetSubmissions()" is deprecated, use "fvcn_list_submissions()" instead!', E_USER_NOTICE);
	fvcn_list_submissions();
}


function fvcn_archives() {
	$arch = new fvCommunityNewsSubmissionsArchive();
	$arch->render();
}



