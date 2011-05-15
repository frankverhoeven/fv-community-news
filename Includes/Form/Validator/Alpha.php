<?php

/**
 *		Alpha.php
 *		FvCommunityNews_Form_Validator_Alpha
 *
 *		Alpha
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Validator_Alpha extends FvCommunityNews_Form_Validator {
	
	/**
	 *	Error Message
	 *	@var string
	 */
	protected $_message = 'This field may only contain alphabetic characters.';
	
	/**
	 *	Allow whitespaces
	 *	@var bool
	 */
	protected $_allowWhiteSpace = false;
	
	/**
	 *		__construct()
	 *
	 *		@param bool $allowWhiteSpace
	 */
	public function __construct($allowWhiteSpace=false) {
		parent::__construct();
		
		$this->setAllowWhiteSpace($allowWhiteSpace);
	}
	
	/**
	 *		setAllowWhiteSpace()
	 *
	 *		@param bool $allowWhiteSpace
	 *		@return object $this
	 */
	public function setAllowWhiteSpace($allowWhiteSpace=false) {
		$this->_allowWhiteSpace = (bool) $allowWhiteSpace;
		
		return $this;
	}
	
	/**
	 *		getAllowWhiteSpace()
	 *
	 *		@return bool
	 */
	public function getAllowWhiteSpace() {
		return $this->_allowWhiteSpace;
	}
	
	/**
	 *		isValid()
	 *
	 *		@param mixed $value
	 *		@return bool
	 */
	public function isValid($value) {
		if ($this->getAllowWhiteSpace()) {
			$value = str_replace(' ', '', $value);
		}
		
		return ctype_alpha($value);
	}
	
}
