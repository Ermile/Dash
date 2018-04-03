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
	public static function __callStatic($_variable, $_args)
	{
		if(array_key_exists(0, $_args))
		{
			// we have parameter 1, want to set variable
			$my_value = $_args[0];

			// want to append as array, do some more works
			if(array_key_exists(1, $_args))
			{
				$my_key        = $_args[1];
				$current_value = [];
				// if have old value, get it. else add as array
				if(isset(self::$data[$_variable]) && is_array(self::$data[$_variable]))
				{
					$current_value = self::$data[$_variable];
				}

				// we have current value and need to add new value
				// create my value as array
				$my_value = [$my_value => true];

				$new_value = array_merge($current_value, $my_value);


			}
			else
			{
				// simply add as new value
				self::$data[$_variable] = $my_value;
				return self::$data[$_variable];
			}

		}
		else
		{
			// on get method
			if(array_key_exists($_variable, self::$data))
			{
				return self::$data[$_variable];
			}
		}

		// return null if nothing founded!
		return null;
	}
}
?>