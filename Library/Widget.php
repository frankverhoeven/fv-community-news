<?php


abstract class fvCommunityNewsWidget {
	
	protected $_view = null;
	
	protected $_settings = null;
	
	public function __construct() {
		$this->_view = fvCommunityNewsRegistry::get('view');
		$this->_settings = fvCommunityNewsSettings::getInstance();
	}
	
	
	
	abstract public function settings();
	
	abstract public function render($args);
	
}

