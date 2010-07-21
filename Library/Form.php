<?php


abstract class fvCommunityNewsForm {
	
	protected $_settings;
	
	protected $_elements = array();
	
	protected $_isValid = null;
	
	protected $_errors = array();
	
	public function __construct() {
		$this->_settings = fvCommunityNewsSettings::getInstance();
		$this->init();
		
	}
	
	abstract public function init();
	
	public function getValue($key) {
		if (isset($_POST[ $key ])) {
			return stripslashes($_POST[ $key ]);
		}
		
		return;
	}
	
	public function getUnfilteredValue($key) {
		
		
	}
	
	public function getValues() {
		foreach ($_POST as $key=>$val) {
			$_POST[ $key ] = stripslashes($_POST[ $key ]);
		}
		
		return $_POST;
	}
	
	public function getUnfilteredValues() {
		
		
	}
	
	public function isPost() {
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			foreach ($this->_elements as $name=>$element) {
				if (isset($_POST[ $name ])) {
					$element->setValue($_POST[ $name ]);
				}
			}
			return true;
		}
		return false;
	}
	
	
	
	public function addElement($name) {
		$this->_elements[ $name ] = new fvCommunityNewsForm_Element($name);
		return $this->_elements[ $name ];
	}
	
	public function getElement($name) {
		if (array_key_exists($name, $this->_elements)) {
			return $this->_elements[ $name ];
		}
	}
	
	public function isValid() {
		if (is_bool($this->_isValid)) {
			return $this->_isValid;
		}
		
		$this->_isValid = true;
		
		foreach ($this->_elements as $name=>$element) {
			if (!$element->isValid()) {
				$this->_errors[ $name ] = $element->getMessage(true);
				$this->_isValid = false;
			}
		}
		
		return $this->_isValid;
	}
	
	public function getErrors() {
		return $this->_errors;
	}
	
}

