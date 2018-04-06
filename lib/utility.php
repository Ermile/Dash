<?php
namespace dash;
class utility
{
	public static $REQUEST;


	public static function request()
	{
		if(!self::$REQUEST)
		{
			self::$REQUEST = new utility\request();
		}
		return self::$REQUEST->get(...func_get_args());
	}

	public static function isset_request()
	{
		if(!self::$REQUEST)
		{
			self::$REQUEST = new utility\request();
		}
		return self::$REQUEST->isset(...func_get_args());
	}


	/**
	 * Sets the request array.
	 *
	 * @param      array  $_array  The array
	 */
	public static function set_request_array($_array)
	{
		$_array =
		[
			'method'  => 'array',
			'request' => $_array,
		];
		self::$REQUEST = new utility\request($_array);
	}


	/**
	 * Call this funtion for encode or decode your password.
	 * If you pass hashed password func verify that,
	 * else create a new pass to save in db
	 * @param  [type] $_plainPassword  [description]
	 * @param  [type] $_hashedPassword [description]
	 * @return [type]                  [description]
	 */
	public static function hasher($_plainPassword, $_hashedPassword = null, $_not_check_crazy = false)
	{
		$raw_password   = $_plainPassword;
		// custom text to add in start and end of password
		$mystart        = '^_^$~*~';
		$myend          = '~_~!^_^';
		$_plainPassword = $mystart. $_plainPassword. $myend;
		$_plainPassword = md5($_plainPassword);
		$myresult       = null;
		// if requrest verify pass check with
		if($_hashedPassword)
		{
			$myresult = password_verify($_plainPassword, $_hashedPassword);
		}
		else
		{
			if(!$_not_check_crazy)
			{
				if(\lib\utility\passwords::is_crazy($raw_password))
				{
					\lib\notif::error(T_("This password is very simple and guessable, please use stronger password!"));
					return false;
				}
			}

			// create option for creating hash cost
			$myoptions = ['cost' => 7 ];
			$myresult  = password_hash($_plainPassword, PASSWORD_BCRYPT, $myoptions);
		}

		return $myresult;
	}
}
?>