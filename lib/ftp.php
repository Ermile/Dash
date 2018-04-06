<?php
namespace dash;
/**
 * Class for ftp.
 * every ftp function
 * for @example
 *  \dash\ftp::pwd();
 *  \dash\ftp::size('remotefile');
 *  \dash\ftp::get(...);
 */
class ftp
{
	public static $link  = null;
	public static $login = false;

	/**
	 * connect fo fpt server
	 */
	public static function connect($_host = null, $_user = null, $_passowrd = null, $_port = null)
	{
		if(!self::$link)
		{
			$ftp_host = $_host;
			if(!$ftp_host)
			{
				$ftp_host = \dash\option::config('ftp', 'host');
			}

			$ftp_port = $_port;
			if(!$ftp_port)
			{
				$ftp_port = \dash\option::config('ftp', 'port');
			}

			$link = @ftp_connect($ftp_host, $ftp_port);
			if($link)
			{
				self::$link = $link;
				self::login($_user, $_passowrd);
			}
		}
	}


	/**
	 * login in connected ftp server
	 */
	public static function login($_user = null, $_passowrd = null)
	{
		$user = $_user;
		if(!$user)
		{
			$user  = \dash\option::config('ftp', 'user');
		}

		$pass = $_passowrd;
		if(!$_passowrd)
		{
			$pass  = \dash\option::config('ftp', 'pass');
		}

		$login = @ftp_login(self::$link, $user, $pass);

		if($login)
		{
			self::$login = $login;
		}

	}


	/**
	 * call every ftp function if exist
	 *
	 * @param      <type>  $_func  The function
	 * @param      <type>  $_args  The arguments
	 */
	public static function __callStatic($_func, $_args)
	{
		$func_name = 'ftp_'. $_func;
		if(function_exists($func_name))
		{
			self::connect();
			if(self::$login)
			{
				$result = $func_name(self::$link, ...$_args);
				return $result;
			}
		}
		return false;
	}
}
?>