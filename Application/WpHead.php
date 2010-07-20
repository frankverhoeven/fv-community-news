<?php

class fvCommunityNewsWpHead {
	
	protected $_view = null;
	
	protected $_settings = null;
	
	public function __construct() {
		$this->_view = fvCommunityNewsRegistry::get('view');
		$this->_settings = fvCommunityNewsSettings::getInstance();
	}
	
	public function render() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('fvcn-front', WP_PLUGIN_URL . FVCN_PLUGINDIR . '/public/javascript/general.js', 'jquery');
		
		if ($this->_settings->get('RssEnabled')) {
			$wp_rewrite = fvCommunityNewsRegistry::get('wp_rewrite');
			
			if ($wp_rewrite->using_permalinks()) {
				$location = get_option('home') . '/feed/';
			} else {
				$location = get_option('home') . '/?feed=';
			}
			
			$this->_view->rss = $location . $this->_settings->getDefault('RssLocation');
		}
		
		if ($this->_settings->get('IncStyle')) {
			$this->_view->style = true;
			$this->_view->dir = WP_PLUGIN_URL . FVCN_PLUGINDIR;
		}
		
		$this->_view->version = $this->_settings->get('Version');
		$this->_view->render('WpHead');
	}
	
}
