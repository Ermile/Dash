<?php
namespace lib;
/**
 * code if life or no
 */
class code
{
	public static function exit()
	{

		self::force_exit();
	}

	/**
	 * exit the code
	 */
	public static function force_exit()
	{
		exit();
	}
}
?>