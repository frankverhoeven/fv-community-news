<?php


class fvCommunityNewsAdmin_Menu {
	
	protected $_dbTable = null;
	
	protected $_slug = 'fvcn-admin';
	
	public function __construct() {
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
	}
	
	public function addPages() {
		$num = $this->_dbTable->getCount(array('Approved'=>'0'));
		$admin = new fvCommunityNewsAdmin();
		
		add_menu_page(
			__('Manage Community News', 'fvcn'),
			__('Submissions', 'fvcn') . (0 != $num?' <span id="awaiting-mod" class="count-' . $num . '"><span class="submission-count">' . $num . '</span></span>':''),
			'moderate_comments',
			$this->_slug,
			array($admin, 'Admin_ManageSubmissions'),
			'div'
		);
		add_submenu_page($this->_slug, __('Community News Settings', 'fvcn'), __('Settings', 'fvcn'), 'manage_options', 'fvcn-admin-settings', array($admin, 'Admin_Settings'));
		add_submenu_page($this->_slug, __('Community News Uninstall', 'fvcn'), __('Uninstall', 'fvcn'), 'manage_options', 'fvcn-admin-uninstall', array($admin, 'Admin_Uninstall'));
		
		
		// My Submissions
		if (get_option('fvcn_mySubmissions'))
			add_submenu_page('profile.php', __('My Submissions', 'fvcn'), __('My Submissions', 'fvcn'), 'read', 'fvcn-admin-my-submissions', array($admin, 'Admin_MySubmissions'));
	}
	
	public function fixMenu() {
		if (current_user_can('manage_options')) {
			global $submenu;
			//var_dump($submenu);
			foreach ($submenu[$this->_slug] as $key=>$item) {
				$submenu[ $this->_slug ][ $key ] = preg_replace(
					'/' . __('Submissions', 'fvcn') . ' <span id="awaiting-mod" class="count-\d"><span class="submission-count">\d<\/span><\/span>/',
					__('Submissions', 'fvcn'),
					$item
				);
			}
		}
	}
	
}

