<?php

class fvCommunityNewsAdmin {
	
	public function __construct() {
		if (isset($_REQUEST['fvcn_Admin_Request']) && current_user_can('manage_options')) {
			new fvCommunityNewsAdmin_Request($_REQUEST['fvcn_Admin_Request']);
		}
	}
	
	public function __call($method, $args) {
		if ('Admin_ManageSubmissions' == $method && (isset($_GET['fvcn-action']) && 'edit' == $_GET['fvcn-action']) && isset($_GET['fvcn-submission-id'])) {
			$method = 'Admin_EditSubmission';
		}
		
		$file = '/Application/' . str_replace('_', '/', $method) . '.php';
		
		if (file_exists(FVCN_ROOTDIR . $file)) {
			$class = 'fvCommunityNews' . $method;
			
			$controller = new $class();
			$controller->render();
		} else {
			throw new Exception('Admin page not found');
		}
	}
	
	
}

