<?php

/**
 *		Install.php
 *		FvCommunityNews_Install
 *
 *		Installation and update
 *
 *		@version 1.0
 */

class FvCommunityNews_Install {
	
	/**
	 *	Wpdb
	 *	@var object
	 */
	protected $_db = null;
	
	/**
	 *	Settings
	 *	@var object
	 */
	protected $_settings = null;
	
	/**
	 *		__construct()
	 *
	 */
	public function __construct() {
		global $wpdb;
		
		$this->_db = $wpdb;
		$this->_settings = FvCommunityNews_Settings::getInstance();
	}
	
	/**
	 *		isInstalled()
	 *
	 *		@return bool
	 */
	public function isInstalled() {
		if (!get_option($this->_settings->addPrefix('Version'))) {
			return false;
		}
		
		return true;
	}
	
	/**
	 *		isCurrentVersion()
	 *
	 *		@return bool
	 */
	public function isCurrentVersion() {
		if (version_compare($this->_settings->get('Version'), $this->_settings->getDefault('Version'), '<')) {
			return false;
		}
		
		return true;
	}
	
	/**
	 *		installSettings()
	 *
	 *		@return object $this
	 */
	public function installSettings() {
		$settings = $this->_settings->getAll();
		
		foreach ($settings as $name=>$value) {
			switch ($name) {
				case 'DbName' :
					$value =  $this->_db->prefix . $value;
					break;
				case 'AkismetApiKey' :
					$value = get_option('wordpress_api_key') ? get_option('wordpress_api_key') : '';
					break;
				case 'AkismetEnabled' :
					$value = get_option('wordpress_api_key') ? true : false;
					break;
			}
			
			$this->_settings->add($name, $value);
		}
		
		return $this;
	}
	
	/**
	 *		installDatabase()
	 *
	 *		@return object $this
	 */
	public function installDatabase() {
		$dbName = $this->_settings->get('DbName');
		$sql = FvCommunityNews_Config::getDbTable( $dbName );
		
		if (!file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
			throw new Exception('Cannot find "upgrade.php"');
		} else {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta($sql);
		}
		
		return $this;
	}
	
}

