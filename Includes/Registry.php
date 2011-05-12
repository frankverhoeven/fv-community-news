<?php

/**
 *		Registry.php
 *		FvCommunityNews_Registry
 *
 *		Registry
 *
 *		@version 1.0
 */

class FvCommunityNews_Registry {
	
	/**
	 *	Registry
	 *	@var array
	 */
	private $_options = array();
	
	/**
	 *	Instance of this class
	 *	@var object
	 */
	private static $_instance = null;
	
	/**
	 *		__construct()
	 *
	 *		@param array $options
	 */
	public function __construct(array $options=null) {
		if (!empty($options)) {
			if (!is_array($options)) {
				throw new Exception('Invallid options provided');
			}
			
			foreach ($options as $key=>$val) {
				$this->$key = $val;
			}
		}
	}
	
	/**
	 *		__set()
	 *
	 *		@param string $name
	 *		@param mixed $value
	 */
	public function __set($name, $value) {
		$this->_options[ $name ] = $value;
	}
	
	/**
	 *		__get()
	 *
	 *		@param string $name
	 *		@return mixed
	 */
	public function __get($name) {
		if (!array_key_exists($name, $this->_options)) {
			return;
		}
		
		return $this->_options[ $name ];
	}
	
	/**
	 *		setInstance()
	 *
	 *		@param object $instance
	 */
	public static function setInstance(FvCommunityNews_Registry $instance=null) {
		if (null === self::$_instance) {
			if (null === $instance) {
				self::$_instance = new FvCommunityNews_Registry();
			} else {
				if (!($instance instanceof FvCommunityNews_Registry)) {
					throw new Exception('Invallid instance provided');
				}
				
				self::$_intance = $instance;
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
	
	/**
	 *		set()
	 *
	 *		@param string $name
	 *		@param mixed $value
	 */
	public static function set($name, $value) {
		self::getInstance()->$name = $value;
	}
	
	/**
	 *		get()
	 *
	 *		@param string $name
	 *		@return mixed
	 */
	public static function get($name) {
		return self::getInstance()->$name;
	}
	
}

