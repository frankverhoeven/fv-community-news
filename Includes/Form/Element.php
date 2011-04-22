<?php

/**
 *		Element.php
 *		FvCommunityNews_Form_Element
 *
 *		Element interface
 *
 *		@version 1.0
 */

abstract class FvCommunityNews_Form_Element {
	
	/**
	 *	Element name
	 *	@var string
	 */
	protected $_name = '';
	
	/**
	 *	Label
	 *	@var string
	 */
	protected $_label = '';
	
	/**
	 *	CSS Classes
	 *	@var string
	 */
	protected $_cssClass = '';
	
	/**
	 *	Element value
	 *	@var mixed
	 */
	protected $_value = null;
	
	/**
	 *	Validators
	 *	@var array
	 */
	protected $_validators = array();
	
	/**
	 *	Filters
	 *	@var array
	 */
	protected $_filters = array();
	
	/**
	 *	Error format
	 *	@var string
	 */
	protected $_errorFormat = '<ul class="fvcn-error">%errors%</ul>';
	
	/**
	 *	Error prefix
	 *	@var string
	 */
	protected $_errorPrefix = '<li>';
	
	/**
	 *	Error suffix
	 *	@var string
	 */
	protected $_errorSuffix = '</li>';
	
	/**
	 *	Errors
	 *	@var array
	 */
	protected $_errors = array();
	
	/**
	 *	Element format
	 *	@var string
	 */
	protected $_format = '';
	
	/**
	 *	Ajax response format
	 *	@var string
	 */
	protected $_ajaxFormat = '<%id%><error>%error%</error></%id%>';
	
	/**
	 *		__construct()
	 *
	 *		@param string $name
	 *		@param string $label
	 *		@param array $validators
	 *		@param array $filters
	 *		@param string $value
	 */
	public function __construct($name, $label=null, $validators=array(), $filters=array(), $value=null) {
		$this->setName($name);
		$this->setValidators($validators);
		$this->setFilters($filters);
		
		if (null !== $label) {
			$this->setLabel($label);
		}
		if (null !== $value) {
			$this->setValue($value);
		}
		
		if (method_exists($this, 'init')) {
			$this->init();
		}
	}
	
	/**
	 *		setName()
	 *
	 *		@param string $name
	 *		@return object $this
	 */
	public function setName($name) {
		if ('' == (string)$name) {
			throw new Exception('Invallid name provided');
		}
		
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
	 *		getId()
	 *
	 *		@return string
	 */
	public function getId() {
		$id = $this->getName();
		
		if (!strstr($id, '[')) {
			return $id;
		}
		
		if ('[]' == substr($id, -2)) {
			$id = substr($id, 0, strlen($id) - 2);
		}
		$id = str_replace('][', '-', $id);
		$id = str_replace(array(']', '['), '-', $id);
		$id = trim($id, '-');

		return $id;
	}
	
	/**
	 *		setLabel()
	 *
	 *		@var string $label
	 *		@return object $this
	 */
	public function setLabel($label) {
		$this->_label = (string) $label;
		return $this;
	}
	
	/**
	 *		getLabel()
	 *
	 *		@return string
	 */
	public function getLabel() {
		return $this->_label;
	}
	
	/**
	 *		setCssClass()
	 *
	 *		@param string $class
	 *		@return object $this
	 */
	public function setCssClass($class) {
		$this->_cssClass = trim($class);
		return $this;
	}
	
	/**
	 *		addCssClass()
	 *
	 *		@param string $class
	 *		@return object $this
	 */
	public function addCssClass($class) {
		$this->_cssClass .= ' ' . trim($class);
		return $this;
	}
	
	/**
	 *		getCssClass()
	 *
	 *		@return string
	 */
	public function getCssClass() {
		return trim($this->_cssClass);
	}
	
	/**
	 *		addValidator()
	 *
	 *		@param object $validator
	 *		@return object $this
	 */
	public function addValidator($validator) {
		if (!($validator instanceof FvCommunityNews_Form_Validator)) {
			throw new Exception('Invallid validator provided');
		}
		
		$this->_validators[] = $validator;
		
		return $this;
	}
	
	/**
	 *		setValidators()
	 *
	 *		@param array $validators
	 *		@return object $this
	 */
	public function setValidators($validators) {
		if (!is_array($validators)) {
			throw new Exception('Invallid vaidators provided');
		}
		
		if (empty($validators)) {
			return $this;
		}
		
		foreach ($validators as $validator) {
			if (!($validator instanceof FvCommunityNews_Form_Validator)) {
				throw new Exception('Invallid validator provided');
			}
		}
		
		$this->_validators = $validators;
		
		return $this;
	}
	
	/**
	 *		hasValidators()
	 *
	 *		@return bool
	 */
	public function hasValidators() {
		if (is_array($this->_validators) && !empty($this->_validators)) {
			return true;
		}
		
		return false;
	}
	
	/**
	 *		getValidators()
	 *
	 *		@return array
	 */
	public function getValidators() {
		return $this->_validators;
	}
	
	/**
	 *		addFilter()
	 *
	 *		@param object $filter
	 *		@return object $this
	 */
	public function addFilter($filter) {
		if (!($filter instanceof FvCommunityNews_Form_Filter)) {
			throw new Exception('Invallid filter provided');
		}
		
		$this->_filters[] = $filter;
		
		return $this;
	}
	
	/**
	 *		setFilters()
	 *
	 *		@param array $filters
	 *		@return object $this
	 */
	public function setFilters($filters) {
		if (!is_array($filters)) {
			throw new Exception('Invallid filters provided');
		}
		
		if (empty($filters)) {
			return $this;
		}
		
		foreach ($filters as $filter) {
			if (!($filter instanceof FvCommunityNews_Form_Filter)) {
				throw new Exception('Invallid filter provided');
			}
		}
		
		$this->_filters = $filters;
		
		return $this;
	}
	
	/**
	 *		hasFilters()
	 *
	 *		@return bool
	 */
	public function hasFilters() {
		if (is_array($this->_filters) && !empty($this->_filters)) {
			return true;
		}
		
		return false;
	}
	
	/**
	 *		getFilters()
	 *
	 *		@return array
	 */
	public function getFilters() {
		return $this->_filters;
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
	 *		setValue()
	 *
	 *		@param mixed $value
	 *		@return object $this
	 */
	public function setValue($value) {
		$this->_value = $value;
	}
	
	/**
	 *		getValue()
	 *
	 *		@return mixed
	 */
	public function getValue() {
		$value = $this->getRawValue();
		
		if ($this->hasFilters()) {
			foreach ($this->getFilters() as $filter) {
				$value = $filter->filter($value);
			}
		}
		
		return $value;
	}
	
	/**
	 *		getRawValue()
	 *
	 *		@return mixed
	 */
	final public function getRawValue() {
		return $this->_value;
	}
	
	/**
	 *		isValid()
	 *
	 *		@return bool
	 */
	public function isValid() {
		
		if ($this->hasValidators()) {
			foreach ($this->getValidators() as $validator) {
				if (!$validator->isValid($this->getValue(), $this->getId())) {
					$this->setError( $validator->getMessage() );
					$this->addCssClass('error');
					
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 *		setErrorFormat()
	 *
	 *		@param string $format
	 *		@param string $prefix
	 *		@param string $suffix
	 *		@return object $this
	 */
	public function setErrorFormat($format=null, $prefix=null, $suffix=null) {
		if (null !== $format) {
			if (!strstr($format, '%errors%')) {
				throw new Exception('Invallid error format provided');
			}
			
			$this->_errorFormat = $format;
		}
		
		if (null !== $prefix) {
			$this->_errorPrefix = $prefix;
		}
		if (null !== $suffix) {
			$this->_errorSuffix = $suffix;
		}
		
		return $this;
	}
	
	/**
	 *		setError()
	 *
	 *		@param string $error
	 *		@return object $this
	 */
	public function setError($error) {
		$this->_errors[] = $error;
		return $this;
	}
	
	/**
	 *		hasErrors()
	 *
	 *		@return bool
	 */
	public function hasErrors() {
		$errors = $this->getErrors();
		return !empty($errors);
	}
	
	/**
	 *		getErrors()
	 *
	 *		@return array
	 */
	public function getErrors() {
		return $this->_errors;
	}
	
	/**
	 *		renderErrors()
	 *
	 *		@return string
	 */
	public function renderErrors() {
		if (!$this->hasErrors()) {
			return '';
		}
		
		$format = '';
		foreach ($this->getErrors() as $error) {
			$format .= $this->_errorPrefix . $error . $this->_errorSuffix;
		}
		
		return str_replace('%errors%', $format, $this->_errorFormat);
	}
	
	/**
	 *		render()
	 *
	 *		@return string
	 */
	public function render() {
		$format = $this->getFormat();
		
		$format = str_replace('%name%',		$this->getName(),		$format);
		$format = str_replace('%id%',		$this->getId(),			$format);
		$format = str_replace('%label%',	$this->getLabel(),		$format);
		$format = str_replace('%class%',	$this->getCssClass(),	$format);
		$format = str_replace('%value%',	$this->getValue(),		$format);
		$format = str_replace('%error%',	$this->renderErrors(),	$format);
		
		return $format;
	}
	
	/**
	 *		renderAjax()
	 *
	 *		@return string
	 */
	public function renderAjax() {
		$format = $this->getAjaxFormat();
		
		$format = str_replace('%id%', $this->getId(), $format);
		
		
		if (!$this->hasErrors()) {
			$errors = '';
		} else {
			$errors = $this->getErrors();
			$errors = $errors[0];
		}
		
		$format = str_replace('%error%', $errors, $format);
		
		return $format;
	}
	
}
