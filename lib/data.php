<?php
namespace dash;


class data
{
	private static $data = [];


	public static function set($_key, $_value)
	{
		self::$data[$_key] = $_value;
	}


	public static function get($_key = null, $_sub_key = null)
	{
		if(!$_key)
		{
			return self::$data;
		}
		else
		{
			if(array_key_exists($_key, self::$data))
			{
				if(isset($_sub_key))
				{
					if(is_array(self::$data[$_key]) && array_key_exists($_sub_key, self::$data[$_key]))
					{
						return self::$data[$_key][$_sub_key];
					}
					else
					{
						return null;
					}
				}
				else
				{
					return self::$data[$_key];
				}
			}
			else
			{
				return null;
			}
		}
		return null;
	}


	public static function __callStatic($_variable, $_value)
	{
		$sub_key  = null;

		if(strpos($_variable, '_') !== false)
		{
			$sub_key   = substr($_variable, strpos($_variable, '_') + 1);
			$_variable = str_replace('_'. $sub_key, '', $_variable);
		}

		if(array_key_exists(0, $_value))
		{
			$my_value = $_value[0];

			if(!array_key_exists($_variable, self::$data) && $sub_key)
			{
				self::$data[$_variable]           = [];
				self::$data[$_variable][$sub_key] = $my_value;
			}
			elseif(!array_key_exists($_variable, self::$data) && !$sub_key)
			{
				self::$data[$_variable] = $my_value;
			}
			elseif(array_key_exists($_variable, self::$data) && !$sub_key)
			{
				self::$data[$_variable] = $my_value;
			}
			elseif(array_key_exists($_variable, self::$data) && $sub_key)
			{
				if(is_array(self::$data[$_variable]))
				{
					self::$data[$_variable][$sub_key] = $my_value;
				}
				else
				{
					self::$data[$_variable]           = [self::$data[$_variable]];
					self::$data[$_variable][$sub_key] = $my_value;
				}
			}
		}
		else
		{
			// on get method
			if(array_key_exists($_variable, self::$data))
			{
				if($sub_key)
				{
					if(is_array(self::$data[$_variable]) && array_key_exists($sub_key, self::$data[$_variable]))
					{
						return self::$data[$_variable][$sub_key];
					}
					else
					{
						return null;
					}
				}
				else
				{
					return self::$data[$_variable];
				}
			}
			else
			{
				return null;
			}
		}
	}
}
?>