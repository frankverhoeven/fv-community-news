<?php


class fvCommunityNewsModel_Submission {
	
	protected $_id;
	protected $_name;
	protected $_email;
	protected $_title;
	protected $_location;
	protected $_description;
	protected $_views;
	protected $_date;
	protected $_ip;
	protected $_approved;
	
	public function __construct(array $options = null) {
		if (is_array($options)) {
			$this->setOptions($options);
		}
	}
	
	public function __set($name, $value) {
		$method = 'set' . $name;
		
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid submission property "' . $name . '"');
		}
		
		$this->$method($value);
	}
	
	public function __get($name) {
		$method = 'get' . $name;
		
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid submission propert "' . $name . '"');
		}
		
		return $this->$method();
	}
	
	public function setOptions(array $options) {
		$methods = get_class_methods($this);
		
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		
		return $this;
	}
	
	public function setId($id) {
		$this->_id = (int)$id;
		return $this;
	}
	
	public function getId() {
		return $this->_id;
	}
	
	public function setName($name) {
		$this->_name = (string)$name;
		return $this;
	}
	
	public function getName() {
		return $this->_name;
	}
	
	public function setEmail($email) {
		$this->_email = (string)$email;
		return $this;
	}
	
	public function getEmail() {
		return $this->_email;
	}
	
	public function setTitle($title) {
		$this->_title = (string)$title;
		return $this;
	}
	
	public function getTitle() {
		return $this->_title;
	}
	
	public function setLocation($location) {
		$this->_location = (string)$location;
		return $this;
	}
	
	public function getLocation() {
		return $this->_location;
	}
	
	public function setDescription($description) {
		$this->_description = (string)$description;
		return $this;
	}
	
	public function getDescription() {
		return $this->_description;
	}
	
	public function setViews($views) {
		$this->_views = (int)$views;
		return $this;
	}
	
	public function getViews() {
		return $this->_views;
	}
	
	public function setDate($date) {
		$this->_date = (string)$date;
		return $this;
	}
	
	public function getDate() {
		return $this->_date;
	}
	
	public function setIp($ip) {
		$this->_ip = (string)$ip;
		return $this;
	}
	
	public function getIp() {
		return $this->_ip;
	}
	
	public function setApproved($approved) {
		$this->_approved = (string)$approved;
		return $this;
	}
	
	public function getApproved() {
		return $this->_approved;
	}
	
}

