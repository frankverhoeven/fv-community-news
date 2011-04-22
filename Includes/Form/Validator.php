<?php

/**
 *		Validator.php
 *		FvCommunityNews_Form_Validator
 *
 *		Validator interface
 *
 *		@version 1.0
 */

abstract class FvCommunityNews_Form_Validator {
	
	/**
	 *	Error Message
	 *	@var string
	 */
	protected $_message = 'Invallid value provided.';
	
	/**
	 *		__construct()
	 *
	 */
	public function __construct() {
		
	}
	
	/**
	 *		isValid()
	 *
	 *		@param mixed $value
	 *		@return bool
	 */
	abstract public function isValid($value);
	
	/**
	 *		getMessage()
	 *
	 *		@return string
	 */
	public function getMessage() {
		return __($this->_message, 'fvcn');
	}
	
}
