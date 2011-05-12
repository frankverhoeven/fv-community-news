<?php

/**
 *		Filter.php
 *		FvCommunityNews_Form_Filter
 *
 *		Filter interface
 *
 *		@version 1.0
 */

abstract class FvCommunityNews_Form_Filter {
	
	/**
	 *		__construct()
	 *
	 */
	public function __construct() {
		
	}
	
	/**
	 *		filter()
	 *
	 *		@param mixed $value
	 *		@return mixed
	 */
	abstract public function filter($value);
	
}
