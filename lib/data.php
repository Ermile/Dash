<?php
namespace lib;


class data
{
	private static $data = [];


	public static function set($_key, $_value)
	{
		self::$data[$_key] = $_value;
	}


	public static function get($_key = null)
	{
		if(!$_key)
		{
			return self::$data;
		}
		elseif(array_key_exists($_key, self::$data))
		{
			return self::$data[$_key];
		}
		return null;
	}
}
?>