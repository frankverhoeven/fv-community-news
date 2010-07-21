<?php


abstract class fvCommunityNewsAdmin_Abstract {
	
	protected $_view = null;
	
	public function __construct() {
		$this->_view = fvCommunityNewsRegistry::get('view');
		$this->init();
	}
	
	abstract public function init();
	
	abstract public function render();
	
	public function renderNoAccess() {
		$this->_view->render('Admin_NoAccess');
	}
	
}

