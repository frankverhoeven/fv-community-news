<?php

class fvCommunityNewsAdmin_EditSubmission extends fvCommunityNewsAdmin_Abstract {
	
	protected $_dbTable = null;
	
	public function init() {
		if (!current_user_can('manage_options')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
		
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
	}
	
	public function render() {
		$this->_view->submission = $this->_dbTable->get($_GET['fvcn-submission-id']);
		$this->_view->render('Admin_EditSubmission');
	}
	
}

