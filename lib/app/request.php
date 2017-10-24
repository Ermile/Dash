<?php
namespace lib\app;

trait request
{
	private static $REQUEST_APP = [];


	/**
	 * Init request
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function request_init($_args)
	{
		if(is_array($_args))
		{
			$args = \lib\utility\safe::safe($_args);

			self::$REQUEST_APP = $args;
		}
	}


	/**
	 * get request
	 */
	public static function request($_name = null)
	{
		if($_name)
		{
			if(array_key_exists($_name, self::$REQUEST_APP))
			{
				return self::$REQUEST_APP[$_name];
			}

			return null;
		}
		else
		{
			return self::$REQUEST_APP;
		}
	}
}
?>