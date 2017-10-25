<?php
namespace lib;

class user
{
	/**
	 * The user working by system
	 *
	 * @var        <type>
	 */
	private static $USER_ID = null;


	/**
	 * Initial user id
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function init($_user_id)
	{
		self::$USER_ID = $_user_id;
		$_SESSION['auth']['id'] = $_user_id;
	}


	/**
	 * destroy user id
	 */
	public static function destroy()
	{
		self::$USER_ID = null;
		unset($_SESSION['auth']);
	}

	/**
	 * return current version
	 *
	 * @return     string  The current version of dash
	 */
	public static function id()
	{
		if(!isset(self::$USER_ID))
		{
			if(isset($_SESSION['auth']['id']))
			{
				self::$USER_ID = $_SESSION['auth']['id'];
			}
		}

		if(is_numeric(self::$USER_ID))
		{
			return intval(self::$USER_ID);
		}

		return self::$USER_ID;
	}
}
?>
