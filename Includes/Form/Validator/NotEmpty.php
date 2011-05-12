<?php

/**
 *		NotEmpty.php
 *		FvCommunityNews_Form_Validator_NotEmpty
 *
 *		Not empty validator
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Validator_NotEmpty extends FvCommunityNews_Form_Validator {
	
	/**
	 *	Error Message
	 *	@var string
	 */
	protected $_message = 'A value is required.';
	
	/**
	 *		isValid()
	 *
	 *		@param mixed $value
	 *		@return bool
	 */
	public function isValid($value) {
		if (is_array($value) && !empty($value)) {
			return true;
		}
		
		if (is_string($value) && '' != trim($value)) {
			return true;
		}
		
		if (empty($value) || NULL == $value) {
			return false;
		}
		
		return true;
	}
	
}
