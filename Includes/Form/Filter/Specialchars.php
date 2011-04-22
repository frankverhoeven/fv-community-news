<?php

/**
 *		Specialchars.php
 *		FvCommunityNews_Form_Filter_Specialchars
 *
 *		Specialchars
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Filter_Specialchars extends FvCommunityNews_Form_Filter {
	
	/**
	 *		filter()
	 *
	 *		@param mixed $value
	 *		@return mixed
	 */
	public function filter($value) {
		return esc_html($value);
	}
	
}
