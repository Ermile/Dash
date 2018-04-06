<?php
namespace dash;
/**
 * Class for session.
 * save data in session and get it
 */
class session
{
	private static $key       = 'session_storage';
	private static $key_time  = 'session_storage_time';
	private static $key_limit = 'session_storage_time_limit';


	/**
	 * save data in session
	 * by key and cat
	 *
	 * @param      <type>  $_key    The key
	 * @param      <type>  $_value  The value
	 * @param      <type>  $_cat    The cat
	 */
	public static function set($_key, $_value, $_cat = null, $_time = null)
	{
		if($_cat)
		{
			$_SESSION[self::$key][$_cat][$_key] = $_value;

			if($_time && is_numeric($_time))
			{
				$_SESSION[self::$key_time][$_cat][$_key]       = time();
				$_SESSION[self::$key_limit][$_cat][$_key] = $_time;
			}
		}
		else
		{
			$_SESSION[self::$key][$_key] = $_value;
			if($_time && is_numeric($_time))
			{
				$_SESSION[self::$key_time][$_key]       = time();
				$_SESSION[self::$key_limit][$_key] = $_time;
			}
		}
	}


	/**
	 * get data from session
	 * by check key and cat
	 *
	 * @param      <type>  $_key   The key
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_key = null, $_cat = null)
	{
		if($_key)
		{
			if($_cat)
			{
				if(isset($_SESSION[self::$key][$_cat][$_key]))
				{
					if(isset($_SESSION[self::$key_time][$_cat][$_key]) && isset($_SESSION[self::$key_limit][$_cat][$_key]))
					{
						if(time() - intval($_SESSION[self::$key_time][$_cat][$_key]) > intval($_SESSION[self::$key_limit][$_cat][$_key]))
						{
							return null;
						}
					}

					return $_SESSION[self::$key][$_cat][$_key];
				}
				else
				{
					return null;
				}
			}
			else
			{
				if(isset($_SESSION[self::$key][$_key]))
				{
					if(isset($_SESSION[self::$key_time][$_key]) && isset($_SESSION[self::$key_limit][$_key]))
					{
						if(time() - intval($_SESSION[self::$key_time][$_key]) > intval($_SESSION[self::$key_limit][$_key]))
						{
							return null;
						}
					}

					return $_SESSION[self::$key][$_key];
				}
				else
				{
					return null;
				}
			}
		}
		else
		{
			if(!$_cat)
			{
				return $_SESSION[self::$key];
			}
			else
			{
				if(isset($_SESSION[self::$key][$_cat]))
				{
					return $_SESSION[self::$key][$_cat];
				}
				else
				{
					return null;
				}
			}
		}
		return null;
	}
}
?>