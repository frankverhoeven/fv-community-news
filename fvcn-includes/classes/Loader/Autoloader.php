<?php

/**
 * FvCommunityNews_Loader_Autoloader
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
class FvCommunityNews_Loader_Autoloader
{
	protected $_root;
	protected $_loader;

	/**
	 * Constructor.
	 *
	 * @param string $root
	 * @param FvCommunityNews_Loader_Interface $loader
	 * @return void
	 */
	public function __construct($root, FvCommunityNews_Loader_Interface $loader)
	{
		$this->_root   = $root;
		$this->_loader = $loader;
	}

	/**
	 * Convert a classname to the corresponding file.
	 *
	 * @param string $class
	 * @return string
	 */
	public function convertClassNameToFileName($class)
	{
		$file = str_replace('FvCommunityNews', '', str_replace('_', DIRECTORY_SEPARATOR, $class));
		return $file . '.php';
	}

	/**
	 * Autoloader
	 *
	 * @param string $class
	 * @return boolean
	 */
	public function autoload($class)
	{
		$file = $this->convertClassNameToFileName($class);

		try {
			$this->_loader->loadFile( $this->_root . $file );
		} catch (Exception $e) {
			return false;
		}

		return true;
	}
}

