<?php
namespace lib;
/**
 * this lib handle url of our PHP framework, Dash
 * v 0.1
 */
class url
{
	// declare variables
	private static $url = [];

	/**
	 * initialize url and detect them
	 * @return [type] [description]
	 */
	public static function initialize()
	{
		self::$url = [];
	}


	/**
	 * get value from url variable
	 * @param  [type] $_key [description]
	 * @return [type]       [description]
	 */
	public static function get($_key = null)
	{
		if(array_key_exists($_key, self::$url))
		{
			return self::$url[$_key];
		}
		elseif($_key === null)
		{
			return self::$url;
		}

		return null;
	}


	/**
	 * set key and value into array
	 * @param [type] $_key   [description]
	 * @param [type] $_value [description]
	 */
	public static function set($_key, $_value)
	{

	}

	/**
	 * call every url function if exist
	 *
	 * @param      <type>  $_func  The function
	 * @param      <type>  $_args  The arguments
	 */
	public static function __callStatic($_func, $_args)
	{
		if(array_key_exists($_func, self::$url))
		{
			return self::$url[$_func];
		}
		// if cant find this url as function
		return null;
	}

}
?>