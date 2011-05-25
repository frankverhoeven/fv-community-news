<?php

/**
 *		Digit.php
 *		FvCommunityNews_Form_Validator_Digit
 *
 *		Digit
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Validator_Digit extends FvCommunityNews_Form_Validator {
	
	/**
	 *	Error Message
	 *	@var string
	 */
	protected $_message = 'This field may only contain digits.';
	
	/**
	 *		isValid()
	 *
	 *		@param mixed $value
	 *		@return bool
	 */
	public function isValid($value) {
		return ctype_digit($value);
	}
	
}
