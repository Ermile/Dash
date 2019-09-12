<?php
namespace dash\engine;
/**
 * dash main configure
 */
class version
{
	// @var dash engine current version
	const version = '20.0.1';

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
