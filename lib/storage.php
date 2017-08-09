<?php
namespace lib;

class storage
{
	private static $_storage = array();

	public static function set($_key, $_value)
	{
		self::$_storage[$_key] = $_value;
	}

	public static function get($_key = null)
	{
		if(!$_key)
		{
			return self::$_storage;
		}
		elseif(array_key_exists($_key, self::$_storage))
		{
			return self::$_storage[$_key];
		}
		return null;
	}

	public static function __callStatic($_name, $_args)
    {
    	if(preg_match("/^(set|get)_(.+)$/", $_name, $name))
    	{
    		$method = $name[1];
    		return self::$method($name[2], ...$_args);
    	}
    }
}
?>