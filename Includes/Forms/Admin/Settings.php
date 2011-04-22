<?php

/**
 *		Settings.php
 *		FvCommunityNews_Forms_Settings
 *
 *		Configuration
 *
 *		@version 1.0
 */

class FvCommunityNews_Forms_Admin_Settings extends FvCommunityNews_Form {
	
	/**
	 *	Settings Handler
	 *	@var object
	 */
	protected $_settings = null;
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		$this->setName('fvcn-settings')
			 ->setMethod('post')
			 
			 ->setGroupPrefix('<div id="%name%" class="fvcn-tab"><table class="form-table">')
			 ->setGroupSuffix('</table></div>');
		
		$this->_settings = new FvCommunityNews_Settings(FvCommunityNews_Config::getConfig());
		
		$general = array();
		$general[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_AlwaysAdminModeration',
			__('Before a post appears', 'fvcn'),
			array(),
			array(),
			$this->_settings->get('AlwaysAdminModeration'),
			__('An administrator must always approve a post.', 'fvcn')
		);
		$general[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_PreviousApproved',
			'',
			array(),
			array(),
			$this->_settings->get('PreviousApproved'),
			__('Post author must have a previously approved post.', 'fvcn')
		);
		
		$general[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_MailOnSubmission',
			__('Send an email when', 'fvcn'),
			array(),
			array(),
			$this->_settings->get('MailOnSubmission'),
			__('Anyone submits a post.', 'fvcn')
		);
		$general[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_MailOnModeration',
			'',
			array(),
			array(),
			$this->_settings->get('MailOnModeration'),
			__('A post is held for moderation.', 'fvcn')
		);
		
		$general[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_MySubmissions',
			__('My Community News', 'fvcn'),
			array(),
			array(),
			$this->_settings->get('MySubmissions'),
			__('Add a "My Community News" page, where your users can view their submitted posts.', 'fvcn')
		);
		
		$general[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_Tracking',
			__('Tracking', 'fvcn'),
			array(),
			array(),
			$this->_settings->get('Tracking'),
			__('Keep track of how many times a link to a post is clicked (experimental).', 'fvcn')
		);
		
		$this->addGroup('fvcn-general', $general);
		
		
		$antispam = array();
		$antispam[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_AkismetEnabled',
			__('Akismet', 'fvcn'),
			array(),
			array(),
			$this->_settings->get('AkismetEnabled'),
			__('Enable Akismet spam protection.', 'fvcn')
		);
		
		$antispam[] = new FvCommunityNews_Form_Element_Admin_Text(
			'fvcn_AkismetApiKey',
			__('Akismet API Key', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_AkismetApiKey(),
			),
			array(),
			$this->_settings->get('AkismetApiKey')
		);
		
		$antispam[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_LoggedInToPost',
			__('Authentication', 'fvcn'),
			array(),
			array(),
			$this->_settings->get('LoggedInToPost'),
			__('Post author must be logged in.', 'fvcn')
		);
		
		$this->addGroup('fvcn-antispam', $antispam);
		
		
		$appearance = array();
		$appearance[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_IncludeStylesheet',
			__('Include Stylesheet', 'fvcn'),
			array(),
			array(),
			$this->_settings->get('IncludeStylesheet'),
			__('Include a simple stylesheet.', 'fvcn')
		);
		
		$this->addGroup('fvcn-appearance', $appearance);
		
		
		$this->addElement(new FvCommunityNews_Form_Element_Nonce(
			'fvcn-nonce',
			null,
			array(
				new FvCommunityNews_Form_Validator_Nonce(),
			),
			array()
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Admin_Submit(
			'fvcn-submit',
			null,
			array(),
			array(),
			__('Save Changes', 'fvcn')
		));
		
		
	}
	
	/**
	 *		process()
	 *
	 */
	public function process() {
		if ($this->isValid()) {
			
			foreach ($this->_settings->getAll() as $name=>$defaultValue) {
				
				if ($this->hasElement($this->_settings->addPrefix($name))) {
					
					$this->_settings->set($name, $this->getElement($this->_settings->addPrefix($name))->getValue());
					
				}
				
			}
			
			$this->setMessage(__('Settings updated', 'fvcn'));
			$this->setProcessed(true);
		} else {
			$this->setMessage(__('Invallid values entered, please fix', 'fvcn'));
		}
		
		
	}
	
}
