<?php

/**
 *		Empty.php
 *		FvCommunityNews_Form_Validator_Empty
 *
 *		Empty validator
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Validator_Empty extends FvCommunityNews_Form_Validator {
	
	/**
	 *	Error Message
	 *	@var string
	 */
	protected $_message = 'Field has to be empty.';
	
	/**
	 *		isValid()
	 *
	 *		@param mixed $value
	 *		@return bool
	 */
	public function isValid($value) {
		if (is_array($value) && empty($value)) {
			return true;
		}
		
		if (empty($value) || '' == trim($value) || NULL == $value) {
			return true;
		}
		
		return false;
	}
	
}
