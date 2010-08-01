<?php


abstract class fvCommunityNewsSettings_Abstract {
	
	protected $_prefix = null;
	
	private $_config = null;
	
	private static $_instance = null;
	
	public function __construct(fvCommunityNewsConfig $config) {
		if (null === $this->_prefix) {
			throw new Exception('Prefix not set');
		}
		
		$this->_config = $config;
	}
	
	public function get($name) {
		$name = $this->_removePrefix($name);
		
		switch ($this->_config->$name['type']) {
			case 'bool' :
				return (bool)get_option($this->_prefix . $name, $this->_config->$name);
				break;
			case 'int' :
				return (int)get_option($this->_prefix . $name, $this->_config->$name);
				break;
			case 'string' :
				return (string)get_option($this->_prefix . $name, $this->_config->$name);
				break;
			default :
				return get_option($this->_prefix . $name);
		}
	}
	
	public function getDefault($name) {
		$name = $this->_removePrefix($name);
		
		switch ($this->_config->$name['type']) {
			case 'bool' :
				return (bool)$this->_config->$name;
				break;
			case 'int' :
				return (int)$this->_config->$name;
				break;
			case 'string' :
			default :
				return (string)$this->_config->$name;
		}
	}
	
	public function getAll() {
		$array = array();
		foreach ($this->_config->getObj()->children() as $child) {
			$array[] = $child;
		}
		
		return $array;
	}
	
	public function add($name, $value) {
		$name = $this->_removePrefix($name);
		add_option($this->_prefix . $name, $value);
		
		return $this;
	}
	
	public function set($name, $value) {
		$name = $this->_removePrefix($name);
		update_option($this->_prefix . $name, $value);
		
		return $this;
	}
	
	public function setMultiple(array $options) {
		foreach ($options as $name=>$value) {
			$this->set($name, $value);
		}
	}
	
	public function delete($name) {
		$name = $this->_removePrefix($name);
		delete_option($this->_prefix . $name);
	}
	
	public function getPrefix() {
		return $this->_prefix;
	}
	
	protected function _removePrefix($name) {
		if ($this->_prefix == substr($name, 0, strlen($this->_prefix))) {
			$name = substr($name, strlen($this->_prefix)-1);
		}
		
		return $name;
	}
	
	
	public function __set($name, $value) {
		$this->set($name, $value);
	}
	
	public function __get($name) {
		return $this->get($name);
	}
	
	
	public static function setInstance($instance) {
		if (null === self::$_instance) {
			if (!($instance instanceof fvCommunityNewsSettings)) {
				throw new Exception('Invallid var type given, var should be an instance of "fvCommunityNewsSettings".');
			} else {
				self::$_instance = $instance;
			}
		}
		return self::$_instance;
	}
	
	public static function getInstance() {
		return self::$_instance;
	}
	
}

