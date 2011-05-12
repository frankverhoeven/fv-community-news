<?php

/**
 *		Striptags.php
 *		FvCommunityNews_Form_Filter_Striptags
 *
 *		Striptags
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Filter_Striptags extends FvCommunityNews_Form_Filter {
	
	/**
	 *		filter()
	 *
	 *		@param mixed $value
	 *		@return mixed
	 */
	public function filter($value) {
		return wp_filter_nohtml_kses($value);
	}
	
}
