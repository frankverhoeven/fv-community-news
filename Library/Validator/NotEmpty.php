<?php


class fvCommunityNewsValidator_NotEmpty extends fvCommunityNewsValidator {
	
	protected $_errorMessage = 'A value is required.';
	
	public function isValid($data) {
		if (empty($data)) {
			return false;
		}
		return true;
	}
	
}

