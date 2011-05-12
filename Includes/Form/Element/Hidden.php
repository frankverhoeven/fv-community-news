<?php

/**
 *		Hidden.php
 *		FvCommunityNews_Form_Element_Hidden
 *
 *		Text form element
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Element_Hidden extends FvCommunityNews_Form_Element {
	
	/**
	 *	Element html
	 *	@var string
	 */
	protected $_format = '<input type="hidden" name="%name%" id="%id%" class="%class%" value="%value%" />';
	
}
