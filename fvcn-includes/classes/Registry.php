<?php

/**
 * FvCommunityNews_Registry
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
class FvCommunityNews_Registry
{
	/**
	 * @var array
	 */
	private $_options = array();

	/**
	 * @var object
	 */
	private static $_instance;

	/**
	 * __construct()
	 *
	 * @version 20120710
	 * @param array $options
	 */
	public function __construct(array $options=null)
	{
		if (!empty($options)) {
			$this->setOptions($options);
		}
	}

	/**
	 * __set()
	 *
	 * @version 20120710
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key, $value)
	{
		$this->_options[ $key ] = $value;
	}

	/**
	 * __get()
	 *
	 * @version 20120710
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (!isset($this->_options[ $key ])) {
			return;
		}

		return $this->_options[ $key ];
	}

	/**
	 * setOptions()
	 *
	 * @version 20120710
	 * @param array $options
	 * @return FvCommunityNews_Registry
	 */
	public function setOptions(array $options)
	{
		foreach ($options as $key=>$value) {
			$this->$key = $value;
		}
	}

	/**
	 * setInstance()
	 *
	 * @version 20120710
	 * @param FvCommunityNews_Registry $instance
	 */
	public static function setInstance(FvCommunityNews_Registry $instance=null)
	{
		if (null === self::$_instance) {
			if (null === $instance) {
				self::$_instance = new FvCommunityNews_Registry();
			} else {
				self::$_instance = $instance;
			}
		}
	}

	/**
	 * getInstance()
	 *
	 * @version 20120710
	 * @return FvCommunityNews_Registry
	 */
	public static function getInstance()
	{
		self::setInstance();
		return self::$_instance;
	}

	/**
	 * set()
	 *
	 * @version 20120710
	 * @param string $key
	 * @param mixed $value
	 */
	public static function set($key, $value)
	{
		self::getInstance()->$key = $value;
	}

	/**
	 * get()
	 *
	 * @version 20120710
	 * @param string $key
	 * @return mixed
	 */
	public static function get($key)
	{
		return self::getInstance()->$key;
	}
}

