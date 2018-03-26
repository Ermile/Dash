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


	/**
	 * set or get value with function name
	 * @param  [type] $_key
	 * @param  [type] $_val
	 * @return [type]
	 */
	public static function __callStatic($_key, $_val = 12345679)
	{
		if(array_key_exists($_key, self::$data))
		{
			if($_val === 12345679)
			{
				// get something
				return self::$data[$_key];
			}
			else
			{
				// set something
				self::$data[$_key] = $_val;
				return self::$data[$_key];
			}
		}

		// return null if nothing founded!
		return null;
	}

}
?>