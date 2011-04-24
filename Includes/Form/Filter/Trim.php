<?php

/**
 *		Trim.php
 *		FvCommunityNews_Form_Filter_Trim
 *
 *		Trim
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Filter_Trim extends FvCommunityNews_Form_Filter {
	
	/**
	 *		filter()
	 *
	 *		@param mixed $value
	 *		@return mixed
	 */
	public function filter($value) {
		return trim($value);
	}
	
}
