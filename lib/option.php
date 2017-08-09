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
	public static $enter    = [];
	public static $social   = [];
	public static $sms      = [];
	public static $language = [];

	/**
	 * { function_description }
	 */
	public static function _construct()
	{
		if(empty(self::$config))
		{
			// load default option
			if(file_exists('../../saloos/lib/default_option.php'))
			{
				require_once('../../saloos/lib/default_option.php');
			}

			if(file_exists('../option.php'))
			{
				require_once('../option.php');
			}

			if(file_exists('../option.me.php'))
			{
				require_once('../option.me.php');
			}
		}
	}


	/**
	 * get config
	 *
	 * @param      <type>  $_key    The key
	 * @param      <type>  $_value  The value
	 */
	public static function config($_key = null, $_value = null)
	{
		self::_construct();
		if($_key && !$_value)
		{
			if(array_key_exists($_key, self::$config))
			{
				return self::$config[$_key];
			}
			else
			{
				return null;
			}
		}
		elseif($_key && $_value)
		{
			if(isset(self::$config[$_key][$_value]))
			{
				return self::$config[$_key][$_value];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$config;
		}
	}


	/**
	 * get the enter option
	 *
	 * @param      <type>  $_key    The key
	 * @param      <type>  $_value  The value
	 */
	public static function enter($_key = null, $_value = null)
	{
		self::_construct();
		if($_key && !$_value)
		{
			if(array_key_exists($_key, self::$enter))
			{
				return self::$enter[$_key];
			}
			else
			{
				return null;
			}
		}
		elseif($_key && $_value)
		{
			if(isset(self::$enter[$_key][$_value]))
			{
				return self::$enter[$_key][$_value];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$enter;
		}
	}



	/**
	 * get the social network option
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
	 * { function_description }
	 *
	 * @param      <type>  $_key    The key
	 * @param      <type>  $_value  The value
	 */
	public static function sms($_key = null, $_value = null)
	{
		self::_construct();
		if($_key && !$_value)
		{
			if(array_key_exists($_key, self::$sms))
			{
				return self::$sms[$_key];
			}
			else
			{
				return null;
			}
		}
		elseif($_key && $_value)
		{
			if(isset(self::$sms[$_key][$_value]))
			{
				return self::$sms[$_key][$_value];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$sms;
		}
	}


	/**
	 * get language list
	 *
	 * @param      <type>  $_get   The get
	 */
	public static function language($_get = null)
	{
		if($_get === 'list')
		{
			if(isset(self::$language['list']))
			{
				if(is_array(self::$language['list']))
				{
					$temp = [];
					foreach (self::$language['list'] as $key => $value)
					{
						$temp[$value] = \lib\utility\location\languages::get($value, 'localname');
					}
					return $temp;
				}
				else
				{
					return \lib\utility\location\languages::get(self::$language['list'], 'localname');
				}
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
				return self::config('default_language');
			}
		}
		else
		{
			return self::$language;
		}
	}
}
?>