<?php

/**
 *		Text.php
 *		FvCommunityNews_Form_Element_Admin_Text
 *
 *		Text form element
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Element_Admin_Text extends FvCommunityNews_Form_Element {
	
	/**
	 *	Element html
	 *	@var string
	 */
	protected $_format = '<tr valign="top">
	<th scope="row"><label for="%name%">%label%</label></th>
	<td><input type="text" name="%name%" id="%id%" class="%class%" value="%value%" /><span class="description">%info%</span>%error%</td>
</tr>';
	
	/**
	 *		__construct()
	 *
	 *		@param string $name
	 *		@param string $label
	 *		@param array $validators
	 *		@param array $filters
	 *		@param bool $value
	 *		@param string $info
	 */
	public function __construct($name, $label=null, $validators=array(), $filters=array(), $value=null, $info='') {
		parent::__construct($name, $label, $validators, $filters, $value);
		
		$this->setFormat( str_replace('%info%', $info, $this->getFormat()) );
	}
	
}
