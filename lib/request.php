<?php
namespace dash;

class request
{
	private static $POST  = [];
	private static $GET   = [];
	private static $FILES = [];

	/**
	 * filter post and safe it
	 * @param  [type] $_name [description]
	 * @param  [type] $_type [description]
	 * @param  [type] $_arg  [description]
	 * @return [type]        [description]
	 */
	public static function post($_name = null)
	{
		if(!self::$POST)
		{
			self::$POST = \dash\safe::safe($_POST, 'sqlinjection');
		}

		if(isset($_name))
		{
			if(array_key_exists($_name, self::$POST))
			{
				return self::$POST[$_name];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$POST;
		}
	}


	/**
	 * filter get and safe it
	 * @param  [type] $_name [description]
	 * @param  [type] $_arg  [description]
	 * @return [type]        [description]
	 */
	public static function get($_name = null)
	{
		if(!self::$GET)
		{
			self::$GET = \dash\safe::safe($_GET, 'get_url-nottrim');
		}

		if(isset($_name))
		{
			if(array_key_exists($_name, self::$GET))
			{
				return self::$GET[$_name];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$GET;
		}
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
		$request_method = \dash\server::get('REQUEST_METHOD');

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


	public static function is_unload()
	{
		if(isset($_REQUEST['cmd']) && $_REQUEST['cmd'] === 'unload')
		{
			return true;
		}
		return false;
	}


	public static function is_app($_app_model = null)
	{
		$x_app_request = \dash\header::get('x-app-request');

		if($_app_model)
		{
			if($x_app_request === $_app_model)
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
			if(in_array($x_app_request, ['android', 'ios']))
			{
				return $x_app_request;
			}
		}

		return false;
	}


	public static function is_android()
	{
		return self::is_app('android');
	}


	public static function is_ios()
	{
		return self::is_app('ios');
	}

}
?>
