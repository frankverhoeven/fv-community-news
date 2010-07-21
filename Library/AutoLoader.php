<?php


class fvCommunityNewsAutoloader {
	
	protected $_root = '';
	
	protected $_lookupDirs = array(
		'/Library/',
		'/Application/'
	);
	
	private static $_instance = null;
	
	public function __construct($root) {
		$this->_root = $root;
	}
	
	public function autoLoad($className) {
		$className = str_replace('fvCommunityNews', '', $className);
		$file = str_replace('_', '/', $className) . '.php';
		
		foreach ($this->_lookupDirs as $dir) {
			if (file_exists($this->_root . $dir . $file)) {
				require_once $this->_root . $dir. $file;
			}
		}
		
	}
	
}

