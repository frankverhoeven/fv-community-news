<?php


class fvCommunityNewsValidator_Empty extends fvCommunityNewsValidator {
	
	protected $_errorMessage = 'Field must be empty.';
	
	public function isValid($data) {
		if (empty($data)) {
			return true;
		}
		return false;
	}
	
}

