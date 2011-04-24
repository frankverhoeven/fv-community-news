<?php

/**
 *		Email.php
 *		FvCommunityNews_Form_Validator_Email
 *
 *		Email
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Validator_Email extends FvCommunityNews_Form_Validator {
	
	/**
	 *	Error Message
	 *	@var string
	 */
	protected $_message = 'This field has to contain a valid email address.';
	
	/**
	 *		isValid()
	 *
	 *		@param mixed $value
	 *		@return bool
	 */
	public function isValid($value) {
		return is_email($value);
	}
	
}
