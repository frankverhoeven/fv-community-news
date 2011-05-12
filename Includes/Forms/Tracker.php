<?php

/**
 *		Tracker.php
 *		FvCommunityNews_Form_Tracker
 *
 *		Form for tracking page views
 *
 *		@version 1.0
 */

class FvCommunityNews_Forms_Tracker extends FvCommunityNews_Form {
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		$this->setName('fvcn-tracker');
		$this->setMethod('post');
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-url',
			null,
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
			),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Striptags(),
			)
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Hidden(
			'fvcn-ajax',
			null,
			array(),
			array(),
			'false'
		));
		
	}
	
	/**
	 *		process()
	 *
	 */
	public function process() {
		if ($this->isValid()) {
			$mapper = new FvCommunityNews_Models_PostMapper();
			$mapper->addView($this->getElement('fvcn-url')->getValue());
				
			$this->setProcessed(true);
			$this->setMessage(__('Tracker added.', 'fvcn'));
		} else {
			$this->setMessage(__('No valid url provided.'));
		}
		
		if ('true' == $this->getElement('fvcn-ajax')->getValue()) {
			header('Content-Type: text/xml');
			echo $this->renderAjax();
			exit;
		}
	}
	
}
