<?php

/**
 *		Settings.php
 *		FvCommunityNews_Settings
 *
 *		Settings
 *
 *		@version 1.0
 */

class FvCommunityNews_Settings {
	
	/**
	 *	Options prefix
	 *	@var string
	 */
	private $_prefix = 'fvcn_';
	
	/**
	 *	Default config
	 *	@var array
	 */
	private $_config = array();
	
	/**
	 *	Instance of this class
	 *	@var object
	 */
	private static $_instance = null;
	
	/**
	 *		__construct()
	 *
	 *		@param array $config
	 */
	public function __construct(array $config=null) {
		if (null !== $config) {
			$this->setConfig($config);
		}
	}
	
	/**
	 *		setConfig()
	 *
	 *		@param array $config
	 *		@return object $this
	 */
	public function setConfig(array $config) {
		$this->_config = $config;
		return $this;
	}
	
	/**
	 *		getConfig()
	 *
	 *		@return array
	 */
	public function getConfig() {
		return $this->_config;
	}
	
	/**
	 *		setPrefix()
	 *
	 *		@param string $prefix
	 *		@return object $this
	 */
	public function setPrefix(string $prefix) {
		$this->_prefix = $prefix;
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
	 *		addPrefix()
	 *
	 *		@param string $name
	 *		@return string
	 */
	public function addPrefix($name) {
		if ($this->getPrefix() == substr($name, 0, strlen($this->getPrefix()))) {
			return $name;
		}
		
		return $this->getPrefix() . $name;
	}
	
	/**
	 *		removePrefix()
	 *
	 *		@param string $name
	 *		@return string
	 */
	public function removePrefix($name) {
		if ($this->getPrefix() == substr($name, 0, strlen($this->getPrefix()))) {
			$name = substr($name, strlen($this->getPrefix()));
		}
		
		return $name;
	}
	
	/**
	 *		get()
	 *
	 *		@param string $name
	 *		@return mixed
	 */
	public function get($name) {
		$name = $this->removePrefix($name);
		$value = get_option($this->addPrefix($name), $this->_config[ $name ]['value']);
		
		if (is_serialized($value))
			$value = unserialize( $value );
		
		switch ($this->_config[ $name ]['type']) {
			case 'bool' :
				return (bool) $value;
				break;
			case 'int' :
				return (int) $value;
				break;
			case 'string' :
				return (string) $value;
				break;
			default :
				return $value;
		}
	}
	
	/**
	 *		getDefault()
	 *
	 *		@param string $name
	 *		@return mixed
	 */
	public function getDefault($name) {
		$name = $this->removePrefix($name);
		
		switch ($this->_config[ $name ]['type']) {
			case 'bool' :
				return (bool)$this->_config[ $name ]['value'];
				break;
			case 'int' :
				return (int)$this->_config[ $name ]['value'];
				break;
			case 'string' :
			default :
				return (string)$this->_config[ $name ]['value'];
		}
	}
	
	/**
	 *		getAll()
	 *
	 *		@return array()
	 */
	public function getAll() {
		$settings = array();
		
		foreach ($this->getConfig() as $name=>$val) {
			$settings[ $this->removePrefix($name) ] = $this->getDefault($name);
		}
		
		return $settings;
	}
	
	/**
	 *		add()
	 *
	 *		@param string $name
	 *		@param mixed $value
	 *		@return object $this
	 */
	public function add($name, $value) {
		$name = $this->removePrefix($name);
		$conf = $this->getConfig();
		
		switch ($conf[ $name ]['type']) {
			case 'bool' :
				$value = (bool) $value;
				break;
			case 'int' :
				$value = (int) $value;
				break;
			case 'string' :
			default :
				$value = (string) $value;
		}
		
		$value = serialize($value);
		add_option($this->addPrefix($name), $value);
		
		return $this;
	}
	
	/**
	 *		set()
	 *
	 *		@param string $name
	 *		@param mixed $value
	 *		@return object $this
	 */
	public function set($name, $value) {
		$name = $this->removePrefix($name);
		$conf = $this->getConfig();
		
		switch ($conf[ $name ]['type']) {
			case 'bool' :
				$value = (bool) $value;
				break;
			case 'int' :
				$value = (int) $value;
				break;
			case 'string' :
			default :
				$value = (string) $value;
		}
		
		$value = serialize($value);
		update_option($this->addPrefix($name), $value);
		
		return $this;
	}
	
	/**
	 *		__set()
	 *
	 *		@param string $name
	 *		@param mixed $value
	 */
	public function __set($name, $value) {
		$this->add($name, $value);
	}
	
	/**
	 *		__get()
	 *
	 *		@param string $name
	 *		@return mixed
	 */
	public function __get($name) {
		return $this->get($name);
	}
	
	/**
	 *		setMultiple()
	 *
	 *		@param array $options
	 *		@return object $this
	 */
	public function setMultiple(array $options) {
		foreach ($options as $name=>$value) {
			$this->set($name, $value);
		}
		
		return $this;
	}
	
	/**
	 *		delete()
	 *
	 *		@param string $name
	 *		@return object $this
	 */
	public function delete($name) {
		delete_option($this->addPrefix($name));
		
		return $this;
	}
	
	/**
	 *		setInstance()
	 *
	 *		@param object $instance
	 */
	public static function setInstance(FvCommunityNews_Settings $instance=null) {
		if (null === self::$_instance) {
			if (null === $instance) {
				self::$_instance = new FvCommunityNews_Settings();
			} else {
				if (!($instance instanceof FvCommunityNews_Settings)) {
					throw new Exception('Invallid instance provided');
				}
				
				self::$_instance = $instance;
			}
		}
	}
	
	/**
	 *		getInstance()
	 *
	 *		@return object
	 */
	public static function getInstance() {
		self::setInstance();
		return self::$_instance;
	}
	
}
