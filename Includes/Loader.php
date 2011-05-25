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
	 *	Default files
	 *	@var array
	 */
	private static $_defaultFiles = array(
		'FvCommunityNews_Functions',
		'FvCommunityNews_TemplateFunctions',
	);
	
	/**
	 *	Admin files
	 *	@var array
	 */
	private static $_adminFiles = array(
		'FvCommunityNews_Admin',
	);
	
	/**
	 *	Namespaces
	 *	@var array
	 */
	protected static $_namespaces = array(
		'FvCommunityNews' => 'Includes'
	);
	
	/**
	 *		__construct()
	 *
	 *		@param string $root
	 */
	public function __construct() {
		spl_autoload_register(array(__CLASS__, 'autoload'));
	}
	
	/**
	 *		loadDefault()
	 */
	public static function loadDefault() {
		foreach (self::$_defaultFiles as $class) {
			self::loadClass($class);
		}
	}
	
	/**
	 *		loadAdmin()
	 */
	public static function loadAdmin() {
		foreach (self::$_adminFiles as $class) {
			self::loadClass($class);
		}
	}
	
	/**
	 *		loadClass()
	 *
	 *		@param string $class
	 *		@return bool
	 */
	public static function loadClass($class) {
		foreach (self::$_namespaces as $namespace=>$path) {
			if (strstr($class, $namespace)) {
				$file = FVCN_PLUGIN_DIR . $path . str_replace($namespace, '', str_replace('_', DIRECTORY_SEPARATOR, $class)) . '.php';
				
				if (file_exists($file)) {
					return self::includeFile($file, true);
				} else {
					return false;
				}
			}
		}
		
		return false;
	}
	
	/**
	 *		autoload()
	 *
	 *		@param string $class
	 *		@return bool
	 */
	public static function autoload($class) {
		if (class_exists($class, false) || interface_exists($class, false)) {
			return true;
		}
		
		return self::loadClass($class);
	}
	
	/**
	 *		includeFile()
	 *
	 *		@param string $file
	 *		@param bool $once
	 *		@return bool
	 */
	public static function includeFile($file, $once=false) {
		if ($once) {
			return include_once $file;
		} else {
			return include $file;
		}
	}
	
}

