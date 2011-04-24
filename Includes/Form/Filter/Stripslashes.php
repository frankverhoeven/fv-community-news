<?php

/**
 *		Stripslashes.php
 *		FvCommunityNews_Form_Filter_Stripslashes
 *
 *		Stripslashes
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Filter_Stripslashes extends FvCommunityNews_Form_Filter {
	
	/**
	 *		filter()
	 *
	 *		@param mixed $value
	 *		@return mixed
	 */
	public function filter($value) {
		return stripslashes($value);
	}
	
}
