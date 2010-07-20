<?php


class fvCommunityNewsValidator_Email extends fvCommunityNewsValidator {
	
	protected $_errorMessage = 'Invalid email address.';
	
	public function isValid($data) {
		return is_email($data);
	}
	
}

