<?php

/**
 *		Admin.php
 *		FvCommunityNews_Admin
 *
 *		Admin panels
 *
 *		@version 1.0
 */

class FvCommunityNews_Admin {
	
	/**
	 *	Menu Slug
	 *	@var string
	 */
	protected $_menuSlug = 'fvcn-admin';
	
	/**
	 *	Registry
	 *	@var object
	 */
	protected $_registry = null;
	
	/**
	 *	Settings
	 *	@var object
	 */
	protected $_settings = null;
	
	/**
	 *	Template loader
	 *	@var object
	 */
	protected $_template = null;
	
	/**
	 *	Database mapper
	 *	@var object
	 */
	protected $_dbMapper = null;
	
	/**
	 *		__construct()
	 *
	 */
	public function __construct() {
		if (!is_admin()) {
			return;
		}
		
		$this->_registry = FvCommunityNews_Registry::getInstance();
		$this->_settings = FvCommunityNews_Settings::getInstance();
		$this->_template = FvCommunityNews_Template::getInstance();
		$this->_dbMapper = new FvCommunityNews_Models_PostMapper();
		
		if (method_exists($this, 'init')) {
			$this->init();
		} else {
			$this->_setupActions();
		}
	}
	
	/**
	 *		_setupActions()
	 *
	 */
	private function _setupActions() {
		add_action('admin_menu', array($this, 'addMenu'));
		add_action('admin_menu', array($this, 'removeDoublePostCount'));
		
		// Dashboard widget
		$dashboard = new FvCommunityNews_Admin_Dashboard();
		add_action('wp_dashboard_setup', array($dashboard, 'register'));
		add_action('load-index.php', array($dashboard, 'head'));
		
		// Register scripts/styles
		wp_register_script('fvcn-moderate-posts', FVCN_PLUGIN_URL . 'Public/javascript/admin/moderate-posts.js', array('jquery'), FVCN_VERSION);
		wp_register_script('fvcn-settings-tabs', FVCN_PLUGIN_URL . 'Public/javascript/admin/settings-tabs.js', array('jquery'), FVCN_VERSION);
		wp_register_script('fvcn-add-post', FVCN_PLUGIN_URL . 'Public/javascript/add-post.js', array('jquery'), FVCN_VERSION);
		wp_enqueue_style('fvcn-styles', FVCN_PLUGIN_URL . 'Public/css/admin.css', array('colors'), FVCN_VERSION);
		
		
	}
	
	/**
	 *		addMenu()
	 *
	 */
	public function addMenu() {
		$numPending = $this->_dbMapper->getCount(array('Approved'=>'0'));
		
		$moderationPage = add_menu_page(
			__('FV Community News', 'fvcn'),
			__('Community News', 'fvcn') . '<span id="fvcn-awaiting-mod"' . (0==$numPending?' class="count-0"':'') . '><span id="fvcn-menu-count">' . $numPending . '</span></span>',
			'moderate_comments',
			$this->_menuSlug,
			array($this, 'Moderate'),
			'div'
		);
		
		$settingsPage = add_submenu_page(
			$this->_menuSlug,
			__('FV Community News Settings', 'fvcn'),
			__('Settings', 'fvcn'),
			'manage_options',
			'fvcn-admin-settings',
			array($this, 'Settings')
		);
		add_submenu_page(
			$this->_menuSlug,
			__('FV Community News Uninstall', 'fvcn'),
			__('Uninstall', 'fvcn'),
			'manage_options',
			'fvcn-admin-uninstall',
			array($this, 'Uninstall')
		);
		
		if ($this->_settings->MySubmissions) {
			$myPage = add_submenu_page(
				'users.php',
				__('My Community News', 'fvcn'),
				__('My Community News', 'fvcn'),
				'read',
				'fvcn-admin-my-community-news',
				array($this, 'MyCommunityNews')
			);
			
			add_action('admin_print_styles-' . $myPage, array($this, 'myHead'));
		}
		
		add_action('admin_print_styles-' . $moderationPage,	array($this, 'moderationHead'));
		add_action('admin_print_styles-' . $settingsPage,	array($this, 'settingsHead'));
	}
	
	/**
	 *		removeDoublePostCount()
	 *
	 */
	public function removeDoublePostCount() {
		if (current_user_can('manage_options')) {
			global $submenu;
			
			$submenu[ $this->_menuSlug ][0][0] = preg_replace(
				'/' . __('Community News', 'fvcn') . '<span id="fvcn-awaiting-mod"><span id="fvcn-menu-count">\d<\/span><\/span>/',
				__('Community News', 'fvcn'),
				$submenu[ $this->_menuSlug ][0][0]
			);

		}
	}
	
	/**
	 *		moderationHead()
	 *
	 */
	public function moderationHead() {
		wp_enqueue_script('fvcn-moderate-posts');
	}
	
	/**
	 *		settingsHead()
	 *
	 */
	public function settingsHead() {
		wp_enqueue_script('fvcn-settings-tabs');
	}
	
	/**
	 *		myHead()
	 *
	 */
	public function myHead() {
		wp_enqueue_script('fvcn-add-post');
	}
	
	/**
	 *		__call()
	 *
	 *		@param string $method
	 *		@param array $args
	 */
	public function __call($method, $args) {
		if (isset($_GET['fvcn-action']) && 'edit' == $_GET['fvcn-action']) {
			$class = 'FvCommunityNews_Admin_EditPost';
		} else {
			$class = 'FvCommunityNews_Admin_' . $method;
		}
		
		if (!class_exists($class)) {
			throw new Exception('Invallid admin page selected');
		}
		
		$page = new $class();
		$page->render();
	}
	
}


/**
 *		fvcn_admin()
 *
 */
function fvcn_admin() {
	$FvCommunityNewsAdmin = new FvCommunityNews_Admin();
}
add_action('fvcn_loaded', 'fvcn_admin', 30);

