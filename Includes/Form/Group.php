<?php

/**
 *		Group.php
 *		FvCommunityNews_Form_Group
 *
 *		Groups
 *
 *		@version 1.0
 */

class FvCommunityNews_Form_Group {
	
	/**
	 *	Name
	 *	@var string
	 */
	protected $_name = '';
	
	/**
	 *	Elements
	 *	@var array
	 */
	protected $_elements = array();
	
	/**
	 *	Prefix
	 *	@var string
	 */
	protected $_prefix = '<div id="%name%">';
	
	/**
	 *	Suffix
	 *	@var string
	 */
	protected $_suffix = '</div>';
	
	/**
	 *		__construct()
	 *
	 *		@param array $options
	 */
	public function __construct(array $options=null) {
		if (is_array($options) && !empty($options)) {
			$this->setOptions($options);
		}
	}
	
	/**
	 *		setOptions()
	 *
	 *		@param array $options
	 *		@return object $this
	 */
	public function setOptions(array $options) {
		$methods = get_class_methods($this);
		
		foreach ($options as $key=>$val) {
			$method = 'set' . ucfirst($key);
			
			if (in_array($method, $methods)) {
				$this->$method($val);
			}
		}
		
		return $this;
	}
	
	/**
	 *		setName()
	 *
	 *		@param string $name
	 *		@return bool $this
	 */
	public function setName($name) {
		$this->_name = (string) $name;
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
	 *		setElements()
	 *
	 *		@param array $elements
	 *		@return object $this
	 */
	public function setElements(array $elements) {
		foreach ($elements as $key=>$element) {
			if (!($element instanceof FvCommunityNews_Form_Element)) {
				throw new Exception('Invallid form elements supplied');
			}
			
			$this->addElement($element);
		}
		
		return $this;
	}
	
	/**
	 *		getElements()
	 *
	 *		@return array
	 */
	public function getElements() {
		return $this->_elements;
	}
	
	/**
	 *		addElement()
	 *
	 *		@param object $element
	 *		@return object $this
	 */
	public function addElement(FvCommunityNews_Form_Element $element) {
		$this->_elements[ $element->getName() ] = $element;
		return $this;
	}
	
	/**
	 *		getElement()
	 *
	 *		@param string $name
	 *		@return object
	 */
	public function getElement($name) {
		if (array_key_exists($name, $this->_elements)) {
			return $this->_elements[ $name ];
		}
		
		throw new Exception('Element "' . $name . '" does not exist');
	}
	
	/**
	 *		setPrefix()
	 *
	 *		@param string $prefix
	 *		@return bool $this
	 */
	public function setPrefix($prefix) {
		$this->_prefix = (string) $prefix;
		return $this;
	}
	
	/**
	 *		getPrefix()
	 *
	 *		@return string
	 */
	public function getPrefix() {
		return $this->_prefix;
	}
	
	/**
	 *		setSuffix()
	 *
	 *		@param string $suffix
	 *		@return bool $this
	 */
	public function setSuffix($suffix) {
		$this->_suffix = (string) $suffix;
		return $this;
	}
	
	/**
	 *		getSuffix()
	 *
	 *		@return string
	 */
	public function getSuffix() {
		return $this->_suffix;
	}
	
	/**
	 *		render()
	 *
	 *		@return string
	 */
	public function render() {
		$output = $this->getPrefix();
		
		foreach ($this->getElements() as $name=>$element) {
			$output .= $element->render();
		}
		
		$output .= $this->getSuffix();
		$output = str_replace('%name%', $this->getName(), $output);
		
		return $output;
	}
	
}

