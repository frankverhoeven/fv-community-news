<?php


class fvCommunityNewsForm_Element {
	
	protected $_name = '';
	
	protected $_value = '';
	
	protected $_validators = array();
	
	protected $_message = '';
	
	public function __construct($name) {
		$this->_name = $name;
	}
	
	
	
	
	
	public function setValue($value) {
		$this->_value = $value;
		return $this;
	}
	
	public function getValue() {
		return stripslashes($this->_value);
	}
	
	public function isValid() {
		if (!$this->hasValidators()) {
			return true;
		}
		
		$valid = true;
		
		foreach ($this->_validators as $name=>$validator) {
			if (!$validator->isValid($this->getValue())) {
				$this->_message = $validator->getMessage();
				$valid = false;
				break;
			}
		}
		
		return $valid;
	}
	
	public function setRequired($req=true) {
		if ($req) {
			$this->addValidator('NotEmpty');
		} else {
			$this->removeValidator('NotEmpty');
		}
		
		return $this;
	}
	
	public function hasValidators() {
		return !empty($this->_validators);
	}
	
	public function addValidator($name) {
		$class = 'fvCommunityNewsValidator_' . ucfirst($name);
		$file = WP_PLUGIN_DIR . FVCN_PLUGINDIR . '/Library/Validator/' . ucfirst($name) . '.php';
		
		if (file_exists($file)) {
			$this->_validators[ $name ] = new $class($this->_name);
		}
		
		return $this;
	}
	
	public function removeValidator($name) {
		if (array_key_exists($name, $this->_validators)) {
			unset($this->_validators[ $name ]);
		}
		
		return $this;
	}
	
	
	
	public function getMessage() {
		if ('' != $this->_message) {
			return '<span class="fvcn_ErrorMessage">' . $this->_message . '</span>';
		} else {
			return '';
		}
	}
	
	
}

