<?php

require_once __DIR__ . '/Loader/Interface.php';

/**
 * FvCommunityNews_Loader
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
class FvCommunityNews_Loader implements FvCommunityNews_Loader_Interface
{
	/**
	 * loadFile()
	 *
	 * @param string $file
	 * @param bool $once
	 * @return bool
	 */
	public function loadFile($file, $once=true)
	{
		if (!file_exists($file)) {
			throw new Exception( sprintf('The file "%s" was not found', $file) );
		}

		if (true === $once) {
			return require_once $file;
		} else {
			return require $file;
		}
	}
}

