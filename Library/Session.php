<?php

class fvCommunityNewsSession {
	
	private static $_instance = null;
	
	public function __construct() {
		if (headers_sent() && !session_id()) {
			throw new Exception('Cannot start sessions, headers already sent.');
		}
		if (session_id()) {
			return;
		}
		
		session_start();
	}
	
	public function __set($name, $value) {
		$this->_set($name, $value);
	}
	
	public function __get($name) {
		return $this->_get($name);
	}
	
	protected function _set($name, $value) {
		$_SESSION[ $name ] = serialize($value);
	}
	
	protected function _get($name) {
		return unserialize($_SESSION[ $name ]);
	}
	
	public static function setInstance($instance=null) {
		if (null === self::$_instance) {
			if (null === $instance) {
				self::$_instance = new fvCommunityNewsSession();
			} else {
				if (!($instance instanceof fvCommunityNewsSession)) {
					throw new Exception('Invallid var type given, var should be an instance of "fvCommunityNewsSession".');
				}
				self::$_instance = $instance;
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

