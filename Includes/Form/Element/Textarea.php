<?php

/**
 *		Textarea.php
 *		FvCommunityNews_Form_Element_Text
 *
 *		Text form element
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Element_Textarea extends FvCommunityNews_Form_Element {
	
	/**
	 *	Element html
	 *	@var string
	 */
	protected $_format = '<div><label for="%name%">%label%</label><textarea name="%name%" id="%id%" class="%class%">%value%</textarea>%error%</div>';
	
}
