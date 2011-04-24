<?php

/**
 *		Session.php
 *		FvCommunityNews_Session
 *
 *		Session handler
 *
 *		@version 1.0
 */

class FvCommunityNews_Session {
	
	/**
	 *	Instance
	 *	@var object
	 */
	private static $_instance = null;
	
	/**
	 *		__construct()
	 *
	 */
	public function __construct() {
		$this->start();
	}
	
	/**
	 *		__set()
	 *
	 *		@param string $name
	 *		@param mixed $value
	 */
	public function __set($name, $value) {
		$this->set($name, $value);
	}
	
	/**
	 *		__get()
	 *
	 *		@param string name
	 *		@return mixed
	 */
	public function __get($name) {
		return $this->get($name);
	}
	
	/**
	 *		start()
	 *
	 *		@return object $this
	 */
	public function start() {
		if (headers_sent() && !session_id()) {
			throw new Exception('Cannot start sessions, headers already sent.');
		}
		if (session_id()) {
			return $this;
		}
		
		session_start();
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
		$_SESSION[ $name ] = $value;
		return $this;
	}
	
	/**
	 *		get()
	 *
	 *		@param string $name
	 *		@return mixed
	 */
	public function get($name) {
		if (!$this->exists($name))
			return;
		
		return $_SESSION[ $name ];
	}
	
	/**
	 *		exists()
	 *
	 *		@param string $name
	 *		@return bool
	 */
	public function exists($name) {
		return isset($_SESSION[ $name ]);
	}
	
	/**
	 *		setInstance()
	 *
	 *		@param object $instance
	 */
	public static function setInstance($instance=null) {
		if (null === self::$_instance) {
			if (null === $instance) {
				self::$_instance = new FvCommunityNews_Session();
			} else {
				if (!($instance instanceof FvCommunityNews_Session)) {
					throw new Exception('Invallid instance provided.');
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
