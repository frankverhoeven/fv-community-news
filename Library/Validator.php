<?php


abstract class fvCommunityNewsValidator {
	
	protected $_field = '';
	
	public function __construct($field='') {
		$this->_field = $field;
		
		if (method_exists($this, 'init')) {
			$this->init();
		}
	}
	
	abstract function isValid($data);
	
	public function getMessage() {
		return __($this->_errorMessage, 'fvcn');
	}
	
}

