<?php
namespace dash;

class user
{
	/**
	 * The user working by system
	 *
	 * @var        <type>
	 */
	private static $USER_ID = null;


	public static function init_code($_user_code)
	{
		$user_id = \dash\coding::decode($_user_code);
		if($user_id)
		{
			return self::init($user_id);
		}
	}


	/**
	 * Initial user id
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function init($_user_id)
	{
		if(!is_numeric($_user_id))
		{
			return;
		}

		$detail  = \dash\db\users::get_by_id($_user_id);

		if(!isset($detail['id']))
		{
			return;
		}

		if(is_array($detail))
		{
			foreach ($detail as $key => $value)
			{
				$_SESSION['auth'][$key] = $value;
			}
		}

		self::$USER_ID                 = $_user_id;
		$_SESSION['auth']['id']        = $_user_id;
		$_SESSION['auth']['logintime'] = time();


	}


	public static function refresh()
	{
		$user_id = self::id();
		self::destroy();
		self::init($user_id);
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


	public static function sidebar()
	{
		$sidebar = \dash\user::detail('sidebar');

		if(is_null($sidebar) || $sidebar === '')
		{
			return null;
		}

		if(intval($sidebar) === 1)
		{
			return true;
		}
		elseif($sidebar === '0')
		{
			return false;
		}

		return null;

	}

	/**
	* check is set remember of this user and login by this
	*
	*/
	public static function check_remeber_login()
	{
		// check if have cookie set login by remember
		if(!\dash\user::login())
		{
			$cookie = \dash\db\sessions::get_cookie();
			if($cookie)
			{
				$user_id = \dash\db\sessions::get_user_id();

				if($user_id && is_numeric($user_id))
				{
					self::init($user_id);

					if(isset($_SESSION['main_account']))
					{
						// if the admin user login by this user
						// not save the session
					}
					else
					{
						\dash\db\sessions::set($user_id);
						\dash\log::db('userLoginByRemember');
					}
				}
			}
		}
	}
}
?>
