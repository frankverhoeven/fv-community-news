<?php


class fvCommunityNewsAddSubmission {
	
	protected $_view = null;
	
	public function __construct() {
		$this->_view = fvCommunityNewsRegistry::get('view');
	}
	
	
	
	public function render() {
		
		
		
		
		
		
		
		
		$this->_view->render('Form_AddSubmission');
	}
	
	
}

