<?php


class fvCommunityNewsConfig {
	
	protected $_xmlObj = null;
	
	public function __construct($file) {
		if (!file_exists($file) || !is_readable($file)) {
			throw new Exception('Config file "' . $file . '" can not be read');
		}
		
		$this->_xmlObj = new SimpleXMLElement(file_get_contents($file));
	}
	
	public function __get($key) {
		return $this->_xmlObj->$key;
	}
	
	public function getObj() {
		return $this->_xmlObj;
	}
	
	/*
	public function __set($key, $val) {
		$this->_xmlObj->$key = $val;
	}
	*/
	
}

