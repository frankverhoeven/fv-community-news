<?php

/**
 *	Plugin Name:	FV Community News
 *	Plugin URI:		http://www.frank-verhoeven.com/wordpress-plugin-fv-community-news/
 *	Description:	Give the visitors of your site the ability to submit their news to you, and list it in a nice news feed.
 *	Author:			Frank Verhoeven
 *	Author URI:		http://www.frank-verhoeven.com
 *	Version:		2.0-alpha
 */

define('FVCN_VERSION', '2.0-alpha');

/**
 *		FvCommunityNews.php
 *		FvCommunityNews
 *
 *		Bootstrap
 *
 *		@version 2.0
 */

if (!class_exists('FvCommunityNews')) {
	
	final class FvCommunityNews {
		
		/**
		 *		__construct()
		 *
		 */
		public function __construct() {
			$this->_setupVars();
			$this->_loadFiles();
			$this->_setupActions();
			
			do_action('fvcn_loaded');
		}
		
		/**
		 *		_setupVars()
		 *
		 */
		private function _setupVars() {
			
			define('FVCN_PLUGIN_DIR', plugin_dir_path(__FILE__));
			define('FVCN_PLUGIN_URL', plugin_dir_url(__FILE__));
			
			define('FVCN_BASENAME', plugin_basename(__FILE__));
			
		}
		
		/**
		 *		_loadFiles()
		 *
		 */
		private function _loadFiles() {
			require_once FVCN_PLUGIN_DIR . 'Includes/Loader.php';
			
			FvCommunityNews_Loader::loadDefault();
			if (is_admin()) {
				FvCommunityNews_Loader::loadAdmin();
			}
		}
		
		/**
		 *		_setupActions()
		 *
		 */
		private function _setupActions() {
			register_activation_hook(__FILE__, 'fvcn_activation');
			register_deactivation_hook(__FILE__, 'fvcn_deactivation');
			
			FvCommunityNews_Hooks::hookFilters();
			
			FvCommunityNews_Settings::setInstance(
				new FvCommunityNews_Settings(FvCommunityNews_Config::getConfig())
			);
		}
		
	}
	
	$FvCommunityNews = new FvCommunityNews();
}


/**
 *		fvcn_activation()
 *
 */
function fvcn_activation() {
	register_uninstall_hook(__FILE__, 'fvcn_uninstall');
	
	do_action('fvcn_activation');
}

/**
 *		fvcn_deactivation()
 *
 */
function fvcn_deactivation() {
	
	do_action('fvcn_deactivation');
	
}

/**
 *		fvcn_uninstall()
 *
 */
function fvcn_uninstall() {
	
	do_action('fvcn_uninstall');
	
}








/**
 *
 *		Q.E.D.
 *		Quod Erat Demonstrandum
 *
 */

