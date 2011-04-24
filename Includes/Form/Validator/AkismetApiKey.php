<?php

/**
 *		AkismetApiKey.php
 *		FvCommunityNews_Form_Validator_AkismetApiKey
 *
 *		AkismetApiKey validator
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Validator_AkismetApiKey extends FvCommunityNews_Form_Validator {
	
	/**
	 *	Error Message
	 *	@var string
	 */
	protected $_message = 'Invallid API Key.';
	
	/**
	 *		isValid()
	 *
	 *		@param mixed $value
	 *		@return bool
	 */
	public function isValid($value) {
		if (!is_string($value)) {
			return false;
		}
		
		if ('' == $value) {	// Ignore if empty
			return true;
		}
		
		$akismet = new FvCommunityNews_Akismet(get_option('home'), $value);
		
		if ($akismet->isKeyValid()) {
			return true;
		} else {
			return false;
		}
	}
	
}
