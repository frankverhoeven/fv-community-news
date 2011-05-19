<?php

/**
 *		Nonce.php
 *		FvCommunityNews_Form_Element_Nonce
 *
 *		Nonce field
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Element_Nonce extends FvCommunityNews_Form_Element {
	
	/**
	 *		render()
	 *
	 *		@return string
	 */
	public function render() {
		return wp_nonce_field(
			$this->getId(),
			$this->getId(),
			false,
			false
		);
	}
	
}
