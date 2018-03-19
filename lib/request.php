<?php
namespace lib;

class request
{
	public static $POST;
	public static $GET;
	public static $FILES;

	/**
	 * filter post and safe it
	 * @param  [type] $_name [description]
	 * @param  [type] $_type [description]
	 * @param  [type] $_arg  [description]
	 * @return [type]        [description]
	 */
	public static function post($_name = null, $_type = null, $_arg = null)
	{
		if(!self::$POST)
		{
			self::$POST = \lib\safe::safe($_POST, 'sqlinjection');
		}
		$myvalue = null;
		if(!$_name)
		{
			return self::$POST;
		}
		elseif(is_array($_name))
		{
			$_name = current($_name);
			foreach (self::$POST as $key => $value)
			{
				if (strpos($key, $_name) === 0)
				{
					$myvalue[$key] = $value;
				}
			}
			return $myvalue;
		}
		elseif(isset(self::$POST[$_name]))
		{
			if(is_array(self::$POST[$_name]))
				$myvalue = self::$POST[$_name];
			else
				$myvalue = self::$POST[$_name];


			// if set filter use filter class to clear input value
			if($_type === 'filter')
			{
				if(method_exists('\lib\utility\filter', $_name))
					$myvalue = \lib\utility\filter::$_name($myvalue, $_arg);
			}
			// for password user hasher parameter for hash post value
			elseif($_type === 'hash')
			{
				if($_arg)
				{
					$myvalue = self::hasher($myvalue);
				}
				elseif(mb_strlen($myvalue) > 4 && mb_strlen(mb_strlen($myvalue) < 50))
				{
					$myvalue = self::hasher($myvalue);
				}
				else
				{
					$myvalue = null;
				}
			}

			return $myvalue;
		}

		return null;
	}


	/**
	 * filter get and safe it
	 * @param  [type] $_name [description]
	 * @param  [type] $_arg  [description]
	 * @return [type]        [description]
	 */
	public static function get($_name = null, $_arg = null)
	{
		if(!self::$GET)
		{
			self::$GET = \lib\safe::safe($_GET, 'sqlinjection');
		}
		$myget = [];
		foreach (self::$GET as $key => &$value)
		{
			$pos = strpos($key, '=');
			if($pos)
			{
				$key_t = substr($key, 0, $pos);
				$value = substr($key, $pos+1);
				$myget[$key_t] = $value;
			}
			else
			{
				$myget[$key] = $value;
			}
		}
		self::$GET = $myget;
		unset($myget);

		if($_name)
			return isset(self::$GET[$_name])? self::$GET[$_name] : null;

		elseif(!empty(self::$GET))
		{
			if($_arg === 'raw')
				return self::$GET;
			else
				return ($_arg? '?': null).http_build_query(self::$GET);
		}

		return null;
	}


	/**
	 * get files
	 *
	 * @param      <type>  $_name  The name
	 */
	public static function files($_name = null)
	{
		if(!self::$FILES)
		{
			self::$FILES = $_FILES;
		}

		if($_name)
		{
			if(isset(self::$FILES[$_name]) && (isset(self::$FILES[$_name]['error']) && self::$FILES[$_name]['error'] != 4))
			{
				return self::$FILES[$_name];
			}
			else
			{
				return null;
			}
		}
		return self::$FILES;
	}


	/**
	 * check request method
	 * POST
	 * GET
	 * ...
	 *
	 * @param      <type>   $_name  The name
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function is($_name = null)
	{
		$request_method = \lib\server::get('REQUEST_METHOD');

		if($_name)
		{
			if(mb_strtoupper($_name) === $request_method)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $request_method;
		}
	}


	/**
	 * @return check request is ajax or not
	 */
	public static function ajax()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			return true;
		}

		return false;
	}


	/**
	 * @param  name of accept value to check
	 * @return check accept this type or not in this request
	 */
	public static function accept($name)
	{
		if(isset($_SERVER['HTTP_ACCEPT']))
		{
			return (strpos($_SERVER['HTTP_ACCEPT'], $name) !== false);
		}

		return null;
	}


	/**
	 * @return check json acceptable or not
	 */
	public static function json_accept()
	{
		$result = self::accept("application/json");
		if($result)
		{
			return true;
		}
		elseif(isset($_SERVER['Content-Type']) && preg_match("/application\/json/i", $_SERVER['Content-Type']))
		{
			return true;
		}

		return false;
	}
}
?>
