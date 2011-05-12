<?php

/**
 *		Submit.php
 *		FvCommunityNews_Form_Element_Admin_Submit
 *
 *		Submit form element
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Element_Admin_Submit extends FvCommunityNews_Form_Element {
	
	/**
	 *	CSS Classes
	 *	@var string
	 */
	protected $_cssClass = 'button';
	
	/**
	 *	Element html
	 *	@var string
	 */
	protected $_format = '<p class="submit"><input type="submit" name="%name%" id="%id%" class="button-primary %class%" value="%value%" /></p>';
	
}
