<?php

/**
 *		Template.php
 *		FvCommunityNews_Template
 *
 *		Template Loader
 *
 *		@version 1.0
 */

class FvCommunityNews_Template {
	
	/**
	 *	Template directory
	 *	@var string
	 */
	protected $_templateDir = 'Template/';
	
	/**
	 *	Admin template directory
	 *	@var string
	 */
	protected $_adminTemplateDir = 'Includes/Admin/Template/';
	
	/**
	 *	Custom template directory
	 *	@var string
	 */
	protected $_customTemplateDir = '/fvcn/';
	
	/**
	 *	Custom templates enabled
	 *	@var bool
	 */
	protected $_customTemplateEnabled = false;
	
	/**
	 *	Template files
	 *	@var array
	 */
	protected $_templateFiles = array(
		'ListPosts'			=> array(
			'file'		=> 'list-posts.php',
			'access'	=> 'public',
		),
		'ListPostsWidget'	=> array(
			'file'		=> 'list-posts-widget.php',
			'access'	=> 'public',
		),
		'PostsArchive'	=> array(
			'file'		=> 'posts-archive.php',
			'access'	=> 'public',
		),
		'Form'			=> array(
			'file'		=> 'form.php',
			'access'	=> 'public',
		),
		'FormWidget'		=> array(
			'file'		=> 'form-widget.php',
			'access'	=> 'public',
		),
		'ModeratePosts'		=> array(
			'file'		=> 'moderate-posts.php',
			'access'	=> 'admin',
		),
		'EditPost'		=> array(
			'file'		=> 'edit-post.php',
			'access'	=> 'admin',
		),
		'Settings'			=> array(
			'file'		=> 'settings.php',
			'access'	=> 'admin',
		),
		'Uninstall'			=> array(
			'file'		=> 'uninstall.php',
			'access'	=> 'admin',
		),
		'Dashboard'			=> array(
			'file'		=> 'dashboard.php',
			'access'	=> 'admin',
		),
		'DashboardSettings'	=> array(
			'file'		=> 'dashboard-settings.php',
			'access'	=> 'admin',
		),
		'MyCommunityNews'	=> array(
			'file'		=> 'my-community-news.php',
			'access'	=> 'admin',
		),
	);
	
	/**
	 *	Vars for passing thru
	 *	@var array
	 */
	private $_savedVars = array();
	
	/**
	 *	Class instance
	 *	@var object
	 */
	private static $_instance = null;
	
	
	
	/**
	 *		__construct()
	 *
	 */
	public function __construct() {
		$this->setTemplateDir(FVCN_PLUGIN_DIR . $this->getTemplateDir());
		$this->setCustomTemplateDir(get_theme_root() . $this->getCustomTemplateDir());
	}
	
	/**
	 *		setTemplateDir()
	 *
	 *		@param string $templateDir
	 *		@return object $this
	 */
	public function setTemplateDir($templateDir) {
		if (!is_dir($templateDir)) {
			throw new Exception('Template directory does not exist');
		}
		
		$this->_templateDir = $templateDir;
		return $this;
	}
	
	/**
	 *		getTemplateDir()
	 *
	 *		@return string
	 */
	public function getTemplateDir() {
		return $this->_templateDir;
	}
	
	/**
	 *		setCustomTemplateDir()
	 *
	 *		@param string $customTemplateDir
	 *		@return object $this
	 */
	public function setCustomTemplateDir($customTemplateDir) {
		if (!$this->_customTemplateEnabled) {
			return $this;
		}
		
		if (!is_dir($customTemplateDir)) {
			throw new Exception('Custom template directory does not exist');
		}
		
		$this->_customTemplateDir = $customTemplateDir;
		return $this;
	}
	
	/**
	 *		getCustomTemplateDir()
	 *
	 *		@return string
	 */
	public function getCustomTemplateDir() {
		return $this->_customTemplateDir;
	}
	
	/**
	 *		render()
	 *
	 *		@param string $name
	 */
	public function render($name) {
		if (!array_key_exists($name, $this->_templateFiles)) {
			throw new Exception('Invallid template file selected');
		}
		
		if ('admin' == $this->_templateFiles[ $name ]['access']) {
			$file = FVCN_PLUGIN_DIR . $this->_adminTemplateDir . $this->_templateFiles[ $name ]['file'];
		} else if ($this->_customTemplateEnabled && 'public' == $this->_templateFiles[ $name ]['access']) {
			$file = $this->getCustomTemplateDir() . $this->_templateFiles[ $name ]['file'];
		} else {
			$file = $this->getTemplateDir() . $this->_templateFiles[ $name ]['file'];
		}
		
		if (!file_exists($file)) {
			throw new Exception('Template file does not exist');
		}
		
		require $file;
	}
	
	
	
	/**
	 *		setInstance()
	 *
	 *		@param object $instance
	 */
	public static function setInstance($instance=null) {
		if (null === self::$_instance) {
			if (null === $instance) {
				self::$_instance = new FvCommunityNews_Template();
			} else {
				if (!($instance instanceof FvCommunityNews_Template)) {
					throw new Exception('Invallid var type given, var should be an instance of "FvCommunityNews_Template"');
				}
				self::$_instance = $instance;
			}
		}
	}
	
	/**
	 *		getInstance()
	 *
	 *		@return object
	 */
	public static function getInstance() {
		self::setInstance();
		return self::$_instance;
	}
	
	/**
	 *		__set()
	 *
	 *		@param string $name
	 *		@param mixed $value
	 */
	public function __set($name, $value) {
		$this->_savedVars[ $name ] = $value;
	}
	
	/**
	 *		__get()
	 *
	 *		@param string $name
	 *		@return mixed
	 */
	public function __get($name) {
		if (array_key_exists($name, $this->_savedVars))
			return $this->_savedVars[ $name ];
		return;
	}
	
}
