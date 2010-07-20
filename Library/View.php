<?php


class fvCommunityNewsView {
	
	protected $_viewDir = null;
	
	protected $_savedVars = array();
	
	public function __construct($root) {
		$this->_viewDir = $root . '/Application/View';
	}
	
	public function render($file) {
		$file = str_replace('_', '/', $file);
		
		if (!file_exists($this->_viewDir . '/' . $file . '.php')) {
			throw new Exception('View file "' . $this->_viewDir . '/' . $file . '.php" can not be found.');
		}
		
		require $this->_viewDir . '/' . $file . '.php';
	}
	
	
	
	
	
	public function __set($name, $value) {
		$this->_savedVars[ $name ] = $value;
	}
	
	public function __get($name) {
		if (array_key_exists($name, $this->_savedVars))
			return $this->_savedVars[ $name ];
		return;
	}
	
}

