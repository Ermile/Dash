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
	public static function init($_user_id, $_detail = [])
	{
		self::$USER_ID = $_user_id;
		$_SESSION['auth']['id'] = $_user_id;

		if(is_array($_detail))
		{
			foreach ($_detail as $key => $value)
			{
				$_SESSION['auth'][$key] = $value;
			}
		}
	}


	public static function refresh()
	{
		$user_id = self::id();
		$detail = \lib\db\users::get_by_id($user_id);
		self::destroy();
		self::init($user_id, $detail);
	}


	public static function login($_key = null)
	{
		if($_key === null)
		{
			if(self::id())
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
			return self::detail($_key);
		}
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


	/**
	 * get detail of user
	 *
	 * @param      <type>  $_key   The key
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function detail($_key = null)
	{
		if($_key)
		{
			if(isset($_SESSION['auth'][$_key]))
			{
				return $_SESSION['auth'][$_key];
			}
			return null;
		}
		else
		{
			if(isset($_SESSION['auth']))
			{
				return $_SESSION['auth'];
			}
			return null;
		}
	}
}
?>
