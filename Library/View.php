<?php


class fvCommunityNewsView {
	
	protected $_root = '';
	
	protected $_viewDir = '/Application/View/';
	
	protected $_customEnabled = false;
	
	protected $_customViewDir = '/My/View/';
	
	protected static $_instance = null;
	
	// Whitelist
	protected $_allowedCustomViews = array(
		'SubmissionsArchive',
		'ListSubmissions',
		'Form_AddSubmission',
	);
	
	protected $_savedVars = array();
	
	public function __construct($root) {
		$this->_root = $root;
		
		if (is_dir($this->_root . $this->_customViewDir)) {
			$this->_customEnabled = true;
		}
	}
	
	public function render($name) {
		$file = str_replace('_', '/', $name) . '.php';
		
		if (in_array($name, $this->_allowedCustomViews) && file_exists($this->_root . $this->_customViewDir . $file)) {
			require $this->_root . $this->_customViewDir . $file;
		} elseif (file_exists($this->_root . $this->_viewDir . $file)) {
			require $this->_root . $this->_viewDir . $file;
		} else {
			throw new Exception('View file "' . $this->_root . $this->_viewDir . $file . '" can not be found.');
		}
	}
	
	
	
	
	public function __set($name, $value) {
		$this->_savedVars[ $name ] = $value;
	}
	
	public function __get($name) {
		if (array_key_exists($name, $this->_savedVars))
			return $this->_savedVars[ $name ];
		return;
	}
	
	
	
	
	
	public static function setInstance($instance=null) {
		if (null === self::$_instance) {
			if (null === $instance) {
				self::$_instance = new fvCommunityNewsView(FVCN_ROOTDIR);
			} else {
				if (!($instance instanceof fvCommunityNewsView)) {
					throw new Exception('Invallid var type given, var should be an instance of "fvCommunityNewsView".');
				}
				self::$_instance = $instance;
			}
		}
	}
	
	public static function getInstance() {
		self::setInstance();
		return self::$_instance;
	}
	
}

