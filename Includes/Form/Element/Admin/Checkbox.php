<?php

/**
 *		Checkbox.php
 *		FvCommunityNews_Form_Element_Admin_Checkbox
 *
 *		Checkbox form element
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Element_Admin_Checkbox extends FvCommunityNews_Form_Element {
	
	/**
	 *	Element html
	 *	@var string
	 */
	protected $_format = '<tr valign="top">
	<th scope="row">%label%</th>
	<td><fieldset><legend class="screen-reader-text">%label%</legend><label for="%name%">
		<input type="checkbox" name="%name%" id="%id%" class="%class%" value="true" %checked% /> %info%
	</label>%error%</fieldset></td>
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
	
	/**
	 *		render()
	 *
	 *		@return string
	 */
	public function render() {
		if (true == $this->getValue()) {
			$this->setFormat( str_replace('%checked%', 'checked="checked"', $this->getFormat()) );
		}
		
		$format = $this->getFormat();
		
		$format = str_replace('%name%',		$this->getName(),		$format);
		$format = str_replace('%id%',		$this->getId(),			$format);
		$format = str_replace('%label%',	$this->getLabel(),		$format);
		$format = str_replace('%class%',	$this->getCssClass(),	$format);
		$format = str_replace('%value%',	$this->getValue(),		$format);
		$format = str_replace('%error%',	$this->renderErrors(),	$format);
		
		return $format;
	}
	
}
