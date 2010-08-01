<?php


class fvCommunityNewsValidator_Nonce extends fvCommunityNewsValidator {
	
	protected $_errorMessage = 'Invallid nonce value.';
	
	public function isValid($data) {
		if (check_admin_referer($this->_field, $this->_field)) {
			return true;
		}
		return false;
	}
	
}

