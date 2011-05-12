<?php

/**
 *		Settings.php
 *		FvCommunityNews_Admin_Settings
 *
 *		Settings Community News
 *
 *		@version 1.0
 */

class FvCommunityNews_Admin_Settings extends FvCommunityNews_Admin {
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		if (!current_user_can('manage_options')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
	}
	
	/**
	 *		render()
	 *
	 */
	public function render() {
		
		$this->_template->render('Settings');
		
	}
	
}

