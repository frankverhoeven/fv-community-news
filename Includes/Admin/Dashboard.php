<?php

/**
 *		Dashboard.php
 *		FvCommunityNews_Admin_Dashboard
 *
 *		Dashboard Widget
 *
 *		@version 1.0
 */

class FvCommunityNews_Admin_Dashboard extends FvCommunityNews_Admin {
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		
	}
	
	/**
	 *		head()
	 *
	 */
	public function head() {
		wp_enqueue_script('fvcn-moderate-posts');
	}
	
	/**
	 *		register()
	 *
	 */
	public function register() {
		if (current_user_can('manage_options')) {
			wp_add_dashboard_widget('fvcn-dashboard-widget', __('Recent Community News', 'fvcn'), array($this, 'render'), array($this, 'settings'));
		}
	}
	
	/**
	 *		settings()
	 *
	 */
	public function settings() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['fvcn_NumDashboardListItems']) ) {
			$number = (int)stripslashes($_POST['fvcn_NumDashboardListItems']);
			
			if ($number < 1 || $number > 30) {
				$number = $this->_settings->getDefault('NumDashboardListItems');
			}
			
			$this->_settings->set('NumDashboardListItems', $number);
		}
		
		$this->_template->render('DashboardSettings');
	}
	
	/**
	 *		render()
	 *
	 */
	public function render() {
		
		$this->_template->render('Dashboard');
		
	}
	
}

