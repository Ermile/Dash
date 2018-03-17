<?php
namespace lib;

/**
 * Class for option.
 */
class option
{
	/**
	 * { var_description }
	 *
	 * @var        array
	 */
	public static $config   = [];
	public static $url      = [];
	public static $enter    = [];
	public static $social   = [];
	public static $sms      = [];
	public static $language = [];

	// check loaded option or no to not read file again
	private static $load_option = false;

	/**
	 * { function_description }
	 */
	public static function _construct()
	{
		if(!self::$load_option)
		{
			self::$load_option = true;
			// load default option
			require_once(lib."engine/option_defaults.php");

			if(file_exists(root.'/includes/option/option.php'))
			{
				require_once(root.'/includes/option/option.php');
			}

			if(file_exists(root.'/includes/option/option.me.php'))
			{
				require_once(root.'/includes/option/option.me.php');
			}
		}
	}


	/**
	 * get config
	 * get sms
	 * get ...
	 *
	 * @param      <type>  $_func  The function
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function __callStatic($_func, $_args)
	{
		self::_construct();

		if(!isset(self::${$_func}))
		{
			return null;
		}

		$temp = self::${$_func};

		if(array_key_exists(0, $_args) && !array_key_exists(1, $_args))
		{
			if(array_key_exists($_args[0], $temp))
			{
				return $temp[$_args[0]];
			}
			else
			{
				return null;
			}
		}
		elseif(array_key_exists(0, $_args) && array_key_exists(1, $_args))
		{
			if(isset($temp[$_args[0]][$_args[1]]))
			{
				return $temp[$_args[0]][$_args[1]];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return $temp;
		}
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_key    The key
	 * @param      <type>  $_value  The value
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function social($_key = null, $_value = null)
	{
		self::_construct();

		if($_key && !$_value)
		{
			if(array_key_exists($_key, self::$social))
			{
				return self::$social[$_key];
			}
			else
			{
				if(isset(self::$social['list'][$_key]))
				{
					return self::$social['list'][$_key];
				}
				else
				{
					return null;
				}
			}
		}
		elseif($_key && $_value)
		{
			if(isset(self::$social[$_key][$_value]))
			{
				return self::$social[$_key][$_value];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$social;
		}
	}


	/**
	 * get language list
	 *
	 * @param      <type>  $_get   The get
	 */
	public static function language($_get = null)
	{
		self::_construct();

		if($_get === 'list')
		{
			if(isset(self::$language['list']))
			{
				return self::$language['list'];
			}
		}
		elseif($_get === 'default')
		{
			if(isset(self::$language['default']))
			{
				return self::$language['default'];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$language;
		}
	}
}
?>