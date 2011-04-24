<?php

/**
 *		Request.php
 *		FvCommunityNews_Request
 *
 *		Request Handeling
 *
 *		@version 1.0
 */

class FvCommunityNews_Request {
	
	/**
	 *	Forms Names
	 *	@var array
	 */
	private $_formNames = array(
		'default'	=> array(
			'add-post'			=> 'AddPost',
			'tracker'			=> 'Tracker',
		),
		'admin'		=> array(
			'moderate-posts'	=> 'ModeratePosts',
			'edit-post'			=> 'EditPost',
			'settings'			=> 'Settings',
			'uninstall'			=> 'Uninstall',
		),
	);
	
	/**
	 *	Forms
	 *	@var array
	 */
	private $_forms = array();
	
	/**
	 *		__construct()
	 *
	 */
	public function __construct() {
		add_action('init', array($this, 'init'));
	}
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		$this->loadForms();
		
		if (isset($_REQUEST['fvcn'])) {
			$this->process();
		}
	}
	
	/**
	 *		loadForms()
	 *
	 *		@return object $this
	 */
	public function loadForms() {
		foreach ($this->_formNames['default'] as $name=>$form) {
			$class = 'FvCommunityNews_Forms_' . $form;
			$forms[ $name ] = new $class('fvcn-' . $name);
		}
		
		if (is_admin()) {
			foreach ($this->_formNames['admin'] as $name=>$form) {
				$class = 'FvCommunityNews_Forms_Admin_' . $form;
				$forms[ $name ] = new $class('fvcn-' . $name);
			}
		}
		
		$this->setForms($forms);
		return $this;
	}
	
	/**
	 *		setForms()
	 *
	 *		@param array $forms
	 *		@return object $this
	 */
	public function setForms($forms) {
		if (!is_array($forms)) {
			throw new Exception('Invallid forms provided');
		}
		
		foreach ($forms as $name=>$form) {
			if (!($form instanceof FvCommunityNews_Form)) {
				throw new Exception('Invallid form provided');
			}
		}
		
		$this->_forms = FvCommunityNews_Registry::getInstance()->forms = $forms;
		return $this;
	}
	
	/**
	 *		getForms()
	 *
	 *		@return array
	 */
	public function getForms() {
		return $this->_forms;
	}
	
	/**
	 *		process()
	 *
	 */
	public function process() {
		$forms = $this->getForms();
		
		if (empty($forms)) {
			return;
		}
		
		foreach ($forms as $name=>$form) {
			if ($form->isPost()) {
				$form->process();
				
				if ($form->getElement('fvcn-ajax') && 'true' == $form->getElement('fvcn-ajax')->getValue()) {
					header('Content-Type: text/xml');
					echo $form->renderAjax();
					exit;
				}
			}
		}
	}
	
}

$fvcn_request = new FvCommunityNews_Request();
