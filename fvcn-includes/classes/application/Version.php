<?php

/**
 * FvCommunityNews_Version
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
final class FvCommunityNews_Version
{
	/**
	 * @var string
	 */
	private static $_version = '3.1';

	/**
	 * getVersion()
	 *
	 * @return string
	 */
	public static function getVersion()
	{
		return self::$_version;
	}
}
