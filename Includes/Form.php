<?php

/**
 *		Form.php
 *		FvCommunityNews_Form
 *
 *		Form builder class
 *
 *		@version 1.0
 */

abstract class FvCommunityNews_Form {
	
	/**
	 *	Form name
	 *	@var string
	 */
	protected $_name = '';
	
	/**
	 *	Submit method
	 *	@var string
	 */
	protected $_method = 'post';
	
	/**
	 *	Form format
	 *	@var string
	 */
	protected $_format = '<form name="%name%" id="%id%" method="%method%" class="%name%">%groups%%elements%</form>';
	
	/**
	 *	Ajax response format
	 *	@var string
	 */
	protected $_ajaxFormat = '<?xml version="1.0" encoding="UTF-8"?><%name%>%elements%<fvcn-response><success>%success%</success><message>%message%</message></fvcn-response></%name%>';
	
	/**
	 *	Form elements
	 *	@var array
	 */
	protected $_elements = array();
	
	/**
	 *	Groups
	 *	@var array
	 */
	protected $_groups = array();
	
	/**
	 *	Group prefix
	 *	@var string
	 */
	protected $_groupPrefix = '<div id="%name%">';
	
	/**
	 *	Group suffix
	 *	@var string
	 */
	protected $_groupSuffix = '</div>';
	
	/**
	 *	Processed
	 *	@var bool
	 */
	protected $_processed = false;
	
	/**
	 *	Error Message
	 *	@var string
	 */
	protected $_message = '';
	
	/**
	 *		__construct()
	 *
	 *		@param string $name
	 */
	public function __construct($name, $method=null) {
		$this->setName($name);
		if (null !== $method) {
			$this->setMethod($method);
		}
		
		$this->addElement(new FvCommunityNews_Form_Element_Hidden(
			'fvcn',
			null,
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
			),
			array(),
			$this->getName()
		));
		
		$this->init();
	}
	
	/**
	 *		init()
	 *
	 */
	abstract public function init();
	
	/**
	 *		process()
	 *
	 */
	abstract public function process();
	
	/**
	 *		setName()
	 *
	 *		@param string $name
	 *		@return object $this
	 */
	public function setName($name) {
		if ('' === (string)$name) {
			throw new Exception('Invallid name provided');
		}
		$this->_name = $name;
		
		return $this;
	}
	
	/**
	 *		getName()
	 *
	 *		@return string
	 */
	public function getName() {
		return $this->_name;
	}
	
	/**
	 *		getId()
	 *
	 *		@return string
	 */
	public function getId() {
		return $this->_name;
	}
	
	/**
	 *		setMethod()
	 *
	 *		@param string $method
	 *		@return object $this
	 */
	public function setMethod($method) {
		$method = strtolower($method);
		
		if ('post' != $method && 'get' != $method) {
			throw new Exception('Invallid method provided');
		}
		
		$this->_method = $method;
		return $this;
	}
	
	/**
	 *		getMethod()
	 *
	 *		@return string
	 */
	public function getMethod() {
		return $this->_method;
	}
	
	/**
	 *		addElement()
	 *
	 *		@param FvCommunityNews_Form_Element $element
	 *		@return object $this
	 */
	public function addElement(FvCommunityNews_Form_Element $element) {
		$this->_elements[ $element->getName() ] = $element;
		return $this;
	}
	
	/**
	 *		hasElement()
	 *
	 *		@param string $name
	 *		@return bool
	 */
	public function hasElement($name) {
		return array_key_exists($name, $this->getElements());
	}
	
	/**
	 *		getElement()
	 *
	 *		@param string $name
	 *		@return bool|object
	 */
	public function getElement($name) {
		$elements = $this->getElements();
		
		if (!$this->hasElement($name)) {
			return false;
		}
		
		return $elements[ $name ];
	}
	
	/**
	 *		getNonGroupElements()
	 *
	 *		@return array
	 */
	public function getNonGroupElements() {
		return $this->_elements;
	}
	
	/**
	 *		getElements()
	 *
	 *		@return array
	 */
	public function getElements() {
		if ($this->hasGroup()) {
			$elements = array_merge($this->getAllGroupElements(), $this->getNonGroupElements());
		} else {
			$elements = $this->getNonGroupElements();
		}
		
		return $elements;
	}
	
	/**
	 *		setFormat()
	 *
	 *		@param string $format
	 *		@return object $this
	 */
	public function setFormat($format) {
		$this->_format = $format;
		return $this;
	}
	
	/**
	 *		getFormat()
	 *
	 *		@return string
	 */
	public function getFormat() {
		return $this->_format;
	}
	
	/**
	 *		setAjaxFormat()
	 *
	 *		@param string $format
	 *		@return object $this
	 */
	public function setAjaxFormat($format) {
		$this->_ajaxFormat = $format;
		return $this;
	}
	
	/**
	 *		getAjaxFormat()
	 *
	 *		@return string
	 */
	public function getAjaxFormat() {
		return $this->_ajaxFormat;
	}
	
	/**
	 *		addGroup()
	 *
	 *		@param object $group
	 *		@return object $this
	 */
	public function addGroup(FvCommunityNews_Form_Group $group) {
		$this->_groups[ $group->getName() ] = $group;
		return $this;
	}
	
	/**
	 *		hasGroup()
	 *
	 *		@param string $name
	 *		@return bool
	 */
	public function hasGroup($name=null) {
		if (null === $name) {
			return !empty($this->_groups);
		} else {
			return array_key_exists($name, $this->_groups);
		}
	}
	
	/**
	 *		getGroup()
	 *
	 *		@param string $name
	 *		@return array
	 */
	public function getGroup($name) {
		if (!array_key_exists($name, $this->_groups)) {
			throw new Exception('Group does not exist');
		} else {
			return $this->_groups[ $name ];
		}
	}
	
	/**
	 *		getGroups()
	 *
	 *		@return array
	 */
	public function getGroups() {
		return $this->_groups;
	}
	
	/**
	 *		getAllGroupElements()
	 *
	 *		@return array
	 */
	public function getAllGroupElements() {
		$elements = array();
		
		foreach ($this->getGroups() as $name=>$group) {
			$elements = array_merge($elements, $group->getElements());
		}
		
		return $elements;
	}
	
	/**
	 *		setProcessed()
	 *
	 *		@param bool $processed
	 *		@return object $this
	 */
	public function setProcessed($processed) {
		$this->_processed = (bool) $processed;
		return $this;
	}
	
	/**
	 *		isProcessed()
	 *
	 *		@return bool
	 */
	public function isProcessed() {
		return $this->_processed;
	}
	
	/**
	 *		setMessage()
	 *
	 *		@param string $message
	 *		@return object $this
	 */
	public function setMessage($message) {
		$this->_message = $message;
		return $this;
	}
	
	/**
	 *		hasMessage()
	 *
	 *		@return bool
	 */
	public function hasMessage() {
		$validator = new FvCommunityNews_Form_Validator_NotEmpty();
		return $validator->isValid($this->_message);
	}
	
	/**
	 *		getMessage()
	 *
	 *		@return string
	 */
	public function getMessage() {
		return $this->_message;
	}
	
	/**
	 *		isPost()
	 *
	 *		@return bool
	 */
	public function isPost() {
		if (!isset($_REQUEST['fvcn']) || $_REQUEST['fvcn'] != $this->getName()) {
			return false;
		}
		
		foreach ($this->getElements() as $element) {
			if (isset($_REQUEST[ $element->getName() ])) {
				$element->setValue($_REQUEST[ $element->getName() ]);
			} else {
				$element->setValue(null);
			}
		}
		
		return true;
	}
	
	/**
	 *		isValid()
	 *
	 *		@return bool
	 */
	public function isValid() {
		$valid = true;
		
		foreach ($this->getElements() as $element) {
			if (!$element->isValid()) {
				$valid = false;
			}
		}
		
		return $valid;
	}
	
	/**
	 *		renderElements()
	 *
	 *		@param array $elements
	 *		@return string
	 */
	public function renderElements($elements) {
		$output = '';
		
		foreach ($elements as $element) {
			$output .= $element->render();
		}
		
		return $output;
	}
	
	/**
	 *		renderGroups()
	 *
	 *		@return string
	 */
	public function renderGroups() {
		$groups = '';
		
		foreach ($this->getGroups() as $name=>$group) {
			$groups .= $group->render();
		}
		
		return $groups;
	}
	
	/**
	 *		render()
	 *
	 *		@return string
	 */
	public function render() {
		$format = $this->getFormat();
		
		$format = str_replace('%name%', $this->getName(),		$format);
		$format = str_replace('%id%', $this->getId(),			$format);
		$format = str_replace('%method%', $this->getMethod(),	$format);
		
		if ($this->hasGroup()) {
			$format = str_replace('%groups%', $this->renderGroups(), $format);
		} else {
			$format = str_replace('%groups%', '', $format);
		}
		
		$format = str_replace('%elements%', $this->renderElements($this->getNonGroupElements()), $format);
		
		
		return $format;
	}
	
	/**
	 *		renderAjax()
	 *
	 *		@return string
	 */
	public function renderAjax() {
		$format = $this->getAjaxFormat();
		
		$format = str_replace('%name%', $this->getName(), $format);
		$format = str_replace('%success%', ($this->isProcessed()?'true':'false'), $format);
		$format = str_replace('%message%', $this->getMessage(), $format);
		
		$elements = '';
		foreach ($this->getElements() as $element) {
			$elements .= $element->renderAjax();
		}
		
		$format = str_replace('%elements%', $elements, $format);
		
		return $format;
	}
	
}
