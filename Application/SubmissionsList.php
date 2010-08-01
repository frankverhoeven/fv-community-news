<?php


class fvCommunityNewsSubmissionsList {
	
	protected $_view = null;
	
	protected $_dbTable = null;
	
	protected $_settings = null;
	
	protected $_numSubmissions = false;
	
	protected $_submissionTemplate = '';
	
	public function __construct($num=false, $template='') {
		$this->_view = fvCommunityNewsRegistry::get('view');
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
		$this->_settings = fvCommunityNewsSettings::getInstance();
		
		
		if (is_int((int)$num) && $num > 0) {
			$this->_numSubmissions = $num;
		} else {
			$this->_numSubmissions = $this->_settings->get('NumSubmissions');
		}
		if ('' != $template && is_string($template)) {
			$this->_submissionTemplate = $template;
		} else {
			$this->_submissionTemplate = $this->_settings->get('SubmissionTemplate');
		}
	}
	
	public function render() {
		$this->_view->format = $this->_submissionTemplate;
		$this->_view->submissions = $this->_dbTable->getAll(0, $this->_numSubmissions, array('Approved'=>'1'));
		
		$this->_view->render('ListSubmissions');
	}
	
	
}

