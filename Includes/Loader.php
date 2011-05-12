<?php

/**
 *		Loader.php
 *		FvCommunityNews_Loader
 *
 *		Handles the loading of files
 *
 *		@version 1.0
 */

class FvCommunityNews_Loader {
	
	/**
	 *	Include directory
	 *	@var string
	 */
	private static $_includeDir = 'Includes/';
	
	/**
	 *	Default files
	 *	@var array
	 */
	private static $_defaultFiles = array(
		'FvCommunityNews_Registry',
		'FvCommunityNews_Config',
		'FvCommunityNews_Settings',
		'FvCommunityNews_Install',
		'FvCommunityNews_Session',
		'FvCommunityNews_Models_Post',
		'FvCommunityNews_Models_PostMapper',
		'FvCommunityNews_Form',
		'FvCommunityNews_Form_Element',
		'FvCommunityNews_Form_Element_Text',
		'FvCommunityNews_Form_Element_Textarea',
		'FvCommunityNews_Form_Element_Hidden',
		'FvCommunityNews_Form_Element_Submit',
		'FvCommunityNews_Form_Element_Nonce',
		'FvCommunityNews_Form_Group',
		'FvCommunityNews_Form_Validator',
		'FvCommunityNews_Form_Validator_NotEmpty',
		'FvCommunityNews_Form_Validator_Empty',
		'FvCommunityNews_Form_Validator_Alpha',
		'FvCommunityNews_Form_Validator_Digit',
		'FvCommunityNews_Form_Validator_Alnum',
		'FvCommunityNews_Form_Validator_Email',
		'FvCommunityNews_Form_Validator_Nonce',
		'FvCommunityNews_Form_Filter',
		'FvCommunityNews_Form_Filter_Trim',
		'FvCommunityNews_Form_Filter_Stripslashes',
		'FvCommunityNews_Form_Filter_Striptags',
		'FvCommunityNews_Form_Filter_SpecialChars',
		'FvCommunityNews_Akismet',
		'FvCommunityNews_Forms_AddPost',
		'FvCommunityNews_Forms_Tracker',
		'FvCommunityNews_Request',
		'FvCommunityNews_Functions',
		'FvCommunityNews_TemplateFunctions',
		'FvCommunityNews_Template',
		'FvCommunityNews_Widgets',
		'FvCommunityNews_Hooks',
		'FvCommunityNews_Rss',
		'FvCommunityNews_Mail',
	);
	
	/**
	 *	Admin files
	 *	@var array
	 */
	private static $_adminFiles = array(
		'FvCommunityNews_Admin',
		'FvCommunityNews_Admin_MyCommunityNews',
		'FvCommunityNews_Admin_Dashboard',
		'FvCommunityNews_Admin_Moderate',
		'FvCommunityNews_Admin_EditPost',
		'FvCommunityNews_Admin_Settings',
		'FvCommunityNews_Admin_Uninstall',
		'FvCommunityNews_Form_Element_Admin_Checkbox',
		'FvCommunityNews_Form_Element_Admin_Text',
		'FvCommunityNews_Form_Element_Admin_Submit',
		'FvCommunityNews_Form_Validator_AkismetApiKey',
		'FvCommunityNews_Forms_Admin_ModeratePosts',
		'FvCommunityNews_Forms_Admin_EditPost',
		'FvCommunityNews_Forms_Admin_Settings',
		'FvCommunityNews_Forms_Admin_Uninstall',
	);
	
	/**
	 *		getLibDir()
	 *
	 *		@return string
	 */
	public static function getIncludeDir() {
		return FVCN_PLUGIN_DIR . self::$_includeDir;
	}
	
	/**
	 *		loadDefault()
	 */
	public static function loadDefault() {
		foreach (self::$_defaultFiles as $class) {
			self::load($class);
		}
	}
	
	/**
	 *		loadAdmin()
	 */
	public static function loadAdmin() {
		foreach (self::$_adminFiles as $class) {
			self::load($class);
		}
	}
	
	/**
	 *		load()
	 *
	 *		@var string $className
	 */
	public static function load($className) {
		if (!class_exists($className)) {
			$file = self::getIncludeDir() . str_replace('FvCommunityNews', '', str_replace('_', DIRECTORY_SEPARATOR, $className)) . '.php';
			
			if (file_exists($file)) {
				require_once $file;
			} else {
				throw new Exception('The file: "' . $className . '.php" could not be found');
			}
			
		}
	}
	
	
}

