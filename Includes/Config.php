<?php

/**
 *		Config.php
 *		FvCommunityNews_Config
 *
 *		Default Configuration
 *
 *		@version 2.0
 */

final class FvCommunityNews_Config {
	
	/**
	 *	Default Settings
	 *	@var array
	 */
	private static $_config = array(
		'Version'				=> array(
			'type'	=> 'string',
			'value'	=> FVCN_VERSION,
		),
		'DbVersion'				=> array(
			'type'	=> 'string',
			'value'	=> '2.0',
		),
		'DbName'				=> array(
			'type'	=> 'string',
			'value'	=> 'fv_community_news',
		),
		'FormTitle'				=> array(
			'type'	=> 'string',
			'value'	=> 'Add Community News',
		),
		'ListTitle'				=> array(
			'type'	=> 'string',
			'value'	=> 'Community News',
		),
		'AkismetApiKey'			=> array(
			'type'	=> 'string',
			'value'	=> '',
		),
		'NumListItems'			=> array(
			'type'	=> 'int',
			'value'	=> 4,
		),
		'NumDashboardListItems'	=> array(
			'type'	=> 'int',
			'value'	=> 5,
		),
		'AlwaysAdminModeration'	=> array(
			'type'	=> 'bool',
			'value'	=> true,
		),
		'PreviousApproved'		=> array(
			'type'	=> 'bool',
			'value'	=> true,
		),
		'AutoSpamDeletion'		=> array(
			'type'	=> 'bool',
			'value'	=> true,
		),
		'MailOnSubmission'		=> array(
			'type'	=> 'bool',
			'value'	=> false,
		),
		'MailOnModeration'		=> array(
			'type'	=> 'bool',
			'value'	=> true,
		),
		'IncludeStylesheet'		=> array(
			'type'	=> 'bool',
			'value'	=> true,
		),
		'MySubmissions'			=> array(
			'type'	=> 'bool',
			'value' => true,
		),
		'AkismetEnabled'		=> array(
			'type'	=> 'bool',
			'value'	=> false,
		),
		'LoggedInToPost'		=> array(
			'type'	=> 'bool',
			'value'	=> false,
		),
		'Tracking'				=> array(
			'type'	=> 'bool',
			'value'	=> false,
		),
	);
	
	/**
	 *	Database table structure
	 *	@var string
	 */
	private static $_dbTable = "CREATE TABLE %TableName% (
		Id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
		Name varchar(40) NOT NULL,
		Email varchar(50) NOT NULL,
		Title varchar(100) NOT NULL,
		Location varchar(180) NOT NULL,
		Description text NOT NULL,
		Date datetime NOT NULL,
		Views int(10) unsigned NOT NULL DEFAULT '0',
		Ip varchar(15) NOT NULL,
		Approved varchar(4) NOT NULL,
		PRIMARY KEY (Id),
		KEY Date (Date),
		KEY Email (Email, Name),
		KEY Approved (Approved)
	) ENGINE=MyISAM	DEFAULT CHARSET=utf8;";
	
	/**
	 *		getConfig()
	 *
	 *		@return array
	 */
	public static function getConfig() {
		return self::$_config;
	}
	
	/**
	 *		getDbTable()
	 *
	 *		@param string $tableName
	 *		@return string
	 */
	public static function getDbTable($tableName) {
		return str_replace('%TableName%', $tableName, self::$_dbTable);
	}
	
}
