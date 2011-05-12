<?php

/**
 *		Nonce.php
 *		FvCommunityNews_Form_Validator_Nonce
 *
 *		Nonce validator
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Validator_Nonce extends FvCommunityNews_Form_Validator {
	
	/**
	 *	Error Message
	 *	@var string
	 */
	protected $_message = 'Invalid nonce.';
	
	/**
	 *		isValid()
	 *
	 *		@param mixed $value
	 *		@param string $name
	 *		@return bool
	 */
	public function isValid($value, $name='') {
		return wp_verify_nonce($value, $name);
	}
	
}
