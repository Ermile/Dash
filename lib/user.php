<?php
namespace dash;

class user
{
	/**
	 * The user working by system
	 *
	 * @var        <type>
	 */
	private static $USER_ID     = null;
	private static $USER_DETAIL = [];


	public static function init_tg($_user_code)
	{
		$user_id = \dash\coding::decode($_user_code);

		if($user_id)
		{
			return self::init($user_id, true);
		}
	}


	/**
	 * Initial user id
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function init($_user_id, $_app_mode = false)
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

		if($_app_mode)
		{
			$detail = \dash\app\user::ready($detail);
		}

		if(is_array($detail))
		{
			foreach ($detail as $key => $value)
			{
				if($value === null)
				{
					// nothing
				}
				else
				{
					self::$USER_DETAIL[$key] = $value;
					$_SESSION['auth'][$key] = $value;
				}
			}
		}

		$_SESSION['auth']['logintime'] = time();

		if(!$_app_mode)
		{
			self::$USER_ID                 = $_user_id;
			$_SESSION['auth']['id']        = $_user_id;
		}
		else
		{
			self::$USER_ID                  = \dash\coding::encode($_user_id);
			$_SESSION['auth']['id']         = self::$USER_ID;
			self::$USER_DETAIL['logintime'] = time();
		}
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
		self::$USER_ID     = null;
		self::$USER_DETAIL = [];
		unset($_SESSION['auth']);
	}


	/**
	 * return current version
	 *
	 * @return     string  The current version of dash
	 */
	public static function id($_decode = null)
	{
		if(!isset(self::$USER_ID))
		{
			if(isset($_SESSION['auth']['id']))
			{
				self::$USER_ID = $_SESSION['auth']['id'];
			}
		}
		if($_decode)
		{
			return intval(\dash\coding::decode(self::$USER_ID));
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
		if(empty(self::$USER_DETAIL) && isset($_SESSION['auth']))
		{
			self::$USER_DETAIL = $_SESSION['auth'];
		}

		if($_key)
		{
			if(isset(self::$USER_DETAIL[$_key]))
			{
				return self::$USER_DETAIL[$_key];
			}
			return null;
		}
		else
		{
			if(isset(self::$USER_DETAIL))
			{
				return self::$USER_DETAIL;
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
