<?php


class fvCommunityNewsAdmin_Dashboard extends fvCommunityNewsAdmin_Abstract {
	
	protected $_dbTable = null;
	
	protected $_settings = null;
	
	protected $_submissionsPerPage = 4;
	
	public function init() {
		if (!current_user_can('manage_options')) {
			return;
		}
		
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
		$this->_settings = fvCommunityNewsSettings::getInstance();
		$this->_submissionsPerPage = $this->_settings->get('DashboardNumSubmissions');
	}
	
	public function hook() {
		if (current_user_can('manage_options')) {
			wp_add_dashboard_widget('fvcn-admin-widget', __('Recent Submissions', 'fvcn'), array($this, 'render'), array($this, 'settings'));
		}
	}
	
	public function settings() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['fvcn_DashboardNumSubmissions']) ) {
			$number = (int)stripslashes($_POST['fvcn_DashboardNumSubmissions']);
			
			if ($number < 1 || $number > 30) {
				$number = $this->_settings->getDefault('DashboardNumSubmissions');
			}
			
			$this->_settings->set('DashboardNumSubmissions', $number);
		}
		
		$this->_view->numSubmissions = $this->_submissionsPerPage;
		$this->_view->render('Admin_DashboardSettings');
	}
	
	public function render() {
		$options = array('Approved'=>'!spam');
		
		$this->_view->submissions = $this->_dbTable->getAll(0, $this->_submissionsPerPage, $options);
		$this->_view->render('Admin_Dashboard');
	}
	
}

