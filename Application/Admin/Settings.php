<?php


class fvCommunityNewsAdmin_Settings extends fvCommunityNewsAdmin_Abstract {
	
	public function init() {
		if (!current_user_can('manage_options')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
	}
	
	public function render() {
		$this->_view->settings = fvCommunityNewsSettings::getInstance();
		$this->_view->render('Admin_Settings');
	}
	
}

