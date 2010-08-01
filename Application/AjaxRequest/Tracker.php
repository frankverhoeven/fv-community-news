<?php


class fvCommunityNewsAjaxRequest_Tracker {
	
	protected $_dbTable = null;
	
	public function __construct() {
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
		
	}
	
	public function save() {
		$location = apply_filters('fvcn_PreLocation', urldecode($_REQUEST['fvcn_Location']));
		$this->_dbTable->addView($location);
		exit;
	}
	
}

