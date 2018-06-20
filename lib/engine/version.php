<?php
namespace dash\engine;
/**
 * dash main configure
 */
class version
{
	// @var dash engine current version
	const version = '14.6.7';

	// @var dash engine current commit number
	// now get it automatically from git commands
	// const iversion = 701;

	// @var current version last update date
	// now get it automatically from git last commit date



	/**
	 * return current version
	 *
	 * @return     string  The current version of dash
	 */
	public static function get()
	{
		return self::version;
	}
}
?>
