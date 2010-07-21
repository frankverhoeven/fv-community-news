<?php


class fvCommunityNewsAjaxRequest {
	
	public function __construct() {
		if (!isset($_REQUEST['fvcn-action'])) {
			throw new Exception('Action not set');
		}
		
		switch ($_REQUEST['fvcn-action']) {
			case 'AddSubmission' :
				$this->_processAddSubmission();
				break;
			default :
				return;
		}
	}
	
	protected function _processAddSubmission() {
		$form = new fvCommunityNewsAjaxRequest_AddSubmission();
		$form->render();
	}
	
}

