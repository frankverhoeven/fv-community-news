<?php


class fvCommunityNewsSubmissionsList {
	
	protected $_view = null;
	
	protected $_dbTable = null;
	
	protected $_settings = null;
	
	public function __construct() {
		$this->_view = fvCommunityNewsRegistry::get('view');
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
		$this->_settings = fvCommunityNewsSettings::getInstance();
	}
	
	public function render() {
		$this->_view->format = $this->_settings->get('SubmissionTemplate');
		$this->_view->submissions = $this->_dbTable->getAll(0, $this->_settings->get('NumSubmissions'), array('Approved'=>'1'));
		
		$this->_view->render('ListSubmissions');
	}
	
	
}

