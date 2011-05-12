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
	 *	Forms
	 *	@var array
	 */
	private $_forms = array(
		'add-post'			=> array(
			'name'		=> 'AddPost',
			'access'	=> 'public',
		),
		'tracker'			=> array(
			'name'		=> 'Tracker',
			'access'	=> 'public',
		),
		'moderate-posts'	=> array(
			'name'		=> 'ModeratePosts',
			'access'	=> 'admin',
		),
		'edit-post'			=> array(
			'name'		=> 'EditPost',
			'access'	=> 'admin',
		),
		'settings'			=> array(
			'name'		=> 'Settings',
			'access'	=> 'admin',
		),
		'uninstall'			=> array(
			'name'		=> 'Uninstall',
			'access'	=> 'admin',
		),
	);
	
	/**
	 *	Loaded Forms
	 *	@var array
	 */
	private $_loadedForms = array();
	
	/**
	 *	Class Instance
	 *	@var object
	 */
	private static $_instance = null;
	
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
		if (isset($_REQUEST['fvcn'])) {
			$key = str_replace('fvcn-', '', $_REQUEST['fvcn']);
			
			if (array_key_exists($key, $this->_forms)) {
				$form = $this->_forms[ $key ];
				
				$this->loadForm($key, $form['name'], ('admin' == $form['access']));
				$this->process();
			}
		}
	}
	
	/**
	 *		loadForm()
	 *
	 *		@param string $key
	 *		@param string $name
	 *		@param bool $admin
	 *		@return object
	 */
	public function loadForm($key, $name, $admin=null) {
		if ($admin && !is_admin()) {
			return null;
		}
		
		if ($admin) {
			$class = 'FvCommunityNews_Forms_Admin_' . $name;
		} else {
			$class = 'FvCommunityNews_Forms_' . $name;
		}
		
		if (!class_exists($class)) {
			throw new Exception("Form '$class' does not exist");
		}
		
		$form = new $class('fvcn-' . $key);
		
		$this->addForm(array($key => $form));
		return $form;
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
			if (null === $form) {
				unset($forms[ $name ]);
			}
		}
		
		$this->_loadedForms = $forms;
		return $this;
	}
	
	/**
	 *		addForm()
	 *
	 *		@param array $form
	 *		@return FvCommunityNews_Request
	 */
	public function addForm(array $form) {
		if (!(current($form) instanceof FvCommunityNews_Form)) {
			throw new Exception('Invallid form provided');
		}
		
		$this->_loadedForms = array_merge($this->_loadedForms, $form);
		
		return $this;
	}
	
	/**
	 *		getForms()
	 *
	 *		@return array
	 */
	public function getForms() {
		return $this->_loadedForms;
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
	
	
	/**
	 *		setInstance()
	 *
	 *		@param FvCommunityNews_Request $instance
	 *		@return void
	 */
	public static function setInstance(FvCommunityNews_Request $instance = null) {
		if (null === self::$_instance) {
			if (null === $instance) {
				self::$_instance = new FvCommunityNews_Request();
			} else {
				self::$_instance = $instance;
			}
		}
	}
	
	/**
	 *		getInstance()
	 *
	 *		@return FvCommunityNews_Request
	 */
	public static function getInstance() {
		self::setInstance();
		return self::$_instance;
	}
	
	/**
	 *		getForm()
	 *
	 *		@param string $key
	 *		@return FvCommunityNews_Form
	 */
	public static function getForm($key) {
		$instance = self::getInstance();
		
		if (!array_key_exists($key, $instance->_forms)) {
			throw new Exception("Form key '$key' not found");
		}
		
		if (array_key_exists($key, $instance->_loadedForms)) {
			return $instance->_loadedForms[ $key ];
		}
		
		return $instance->loadForm($key, $instance->_forms[ $key ]['name'], ('admin' == $instance->_forms[ $key ]['access']));
	}
	
	
}

$fvcn_request = new FvCommunityNews_Request();
FvCommunityNews_Request::setInstance( $fvcn_request );

