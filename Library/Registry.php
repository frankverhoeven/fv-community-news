<?php

class fvCommunityNewsRegistry {
	
	private $_options = array();
	
	private static $_instance = null;
	
	public function __construct() {
		
	}
	
	public function __set($name, $value) {
		$this->_options[ $name ] = $value;
	}
	
	public function __get($name) {
		return $this->_options[ $name ];
	}
	
	public static function setInstance($instance=null) {
		if (null === self::$_instance) {
			if (null === $instance) {
				self::$_instance = new fvCommunityNewsRegistry();
			} else {
				if (!($instance instanceof fvCommunityNewsRegistry)) {
					throw new Exception('Invallid var type given, var should be an instance of "fvCommunityNewsRegistry".');
				}
				self::$_intance = $instance;
			}
		}
	}
	
	public static function getInstance() {
		self::setInstance();
		return self::$_instance;
	}
	
	public static function set($name, $value) {
		self::getInstance()->$name = $value;
	}
	
	public static function get($name) {
		return self::getInstance()->$name;
	}
	
}

