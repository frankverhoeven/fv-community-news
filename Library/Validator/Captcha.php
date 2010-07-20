<?php


class fvCommunityNewsValidator_Captcha extends fvCommunityNewsValidator {
	
	protected $_errorMessage = 'Invallid captcha value.';
	
	public function isValid($data) {
		if (sha1($data) == fvCommunityNewsSession::get('fvcn_CaptchaValue')) {
			return true;
		}
		
		return false;
	}
	
}

