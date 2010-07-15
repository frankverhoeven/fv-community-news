<?php


class fvCommunityNewsBootstrap extends fvCommunityNewsBootstrap_Abstract {
	
	protected function _initCaptcha() {
		if (isset($_GET['fvcn-captcha'])) {
			$captcha = new fvCommunityNewsCaptcha();
			$captcha->createText()
					->render();
		}
	}
	
	protected function _initSessions() {
		fvCommunityNewsSession::setInstance();
	}
	
	protected function _initSettings() {
		$config = new fvCommunityNewsConfig(FVCN_ROOTDIR . '/Config/default.xml');
		fvCommunityNewsSettings::setInstance(new fvCommunityNewsSettings($config));
	}
	
	protected function _initInstall() {
		$settings = fvCommunityNewsSettings::getInstance();
		
		if (!get_option($settings->getPrefix() . 'Version')) {
			new fvCommunityNewsInstall($settings);
		} else if (version_compare($settings->get('Version'), $settings->getDefault('Version'), '<')) {
			new fvCommunityNewsUpdate($settings);
		}
	}
	
	protected function _initFilters() {
		new fvCommunityNewsFilters();
	}
	
	protected function _initView() {
		fvCommunityNewsRegistry::set('view', new fvCommunityNewsView(FVCN_ROOTDIR));
	}
	
	protected function _initWpHead() {
		add_action('wp_head', array(new fvCommunityNewsWpHead(), 'addHtml'), 1);
	}
	
	protected function _initPostHooks() {
		add_filter('the_content', array(new fvCommunityNewsPostHooks(), 'fetchHooks'));
	}
	
	
	protected function _initAdminHead() {
		$user = wp_get_current_user();
		if (0 != $user->ID) {
			add_action('admin_head', array(new fvCommunityNewsAdmin_Head(), 'addHtml'), 1);
		}
	}
	
	protected function _initAdmin() {
		$user = wp_get_current_user();
		if (0 != $user->ID) {
			$menu = new fvCommunityNewsAdmin_Menu();
			add_action('admin_menu', array($menu, 'addPages'), 20);
			add_action('admin_menu', array($menu, 'fixMenu'), 60);
			add_action('wp_dashboard_setup', array(new fvCommunityNewsAdmin_Dashboard(), 'hook'));
		}
	}
	
}

