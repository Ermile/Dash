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
			$detail = \dash\app::fix_avatar($detail);
			$detail['fullname'] = self::fullName($detail);
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


	public static function app_id()
	{
		$user_code = \dash\header::get('user_code');

		if($user_code)
		{
			$user_id = \dash\coding::decode($user_code);

			if($user_id)
			{
				return $user_id;
			}
		}
		return null;
	}


	public static function mobile()
	{
		$mobile = \dash\user::detail('mobile');
		if($mobile)
		{
			return $mobile;
		}

		return null;
	}

	public static function chatid()
	{
		$chatid = \dash\user::detail('chatid');
		if($chatid)
		{
			return $chatid;
		}

		return null;
	}


	public static function fullName($_detail = null)
	{
		if(!$_detail || !is_array($_detail))
		{
			$myDetail = \dash\user::detail();
		}
		else
		{
			$myDetail = $_detail;
		}

		if($myDetail)
		{
			$myName = '';
			if(isset($myDetail['firstname']) && isset($myDetail['lastname']))
			{
				$myName = $myDetail['firstname']. ' '. $myDetail['lastname'];
			}
			elseif(isset($myDetail['displayname']))
			{
				$myName = $myDetail['displayname'];
			}
			else
			{
				$myName = T_("Without name");
			}

			if(isset($myDetail['gender']))
			{
				if($myDetail['gender'] === 'male')
				{
					$myName = T_("Mr"). ' '. $myName;
				}
				else if($myDetail['gender'] === 'female')
				{
					$myName = T_("Mrs"). ' '. $myName;
				}
			}

			return $myName;
		}

		return null;
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
		if(\dash\url::content() === 'hook')
		{
			// no check in tg and bank
			return;
		}

		// check if have cookie set login by remember
		if(!\dash\user::login())
		{
			$cookie = \dash\db\sessions::get_cookie();
			if(!$cookie)
			{
				return;
			}

			$user_id = \dash\db\sessions::get_user_id_from_cookie();

			if($user_id && is_numeric($user_id))
			{
				$load_user = \dash\db\users::get_by_id($user_id);

				if(!isset($load_user['id']))
				{
					\dash\db\sessions::terminate_cookie();
					return;
				}

				$logi_by_remember = false;

				if(array_key_exists('forceremember', $load_user))
				{
					if(is_null($load_user['forceremember']))
					{
						// default of this variable is true
						if(\dash\option::config('enter', 'remember_me'))
						{
							$logi_by_remember = true;
						}
						else
						{
							$logi_by_remember = false;
							// no login by remember
						}
					}
					elseif(is_numeric($load_user['forceremember']))
					{
						if(intval($load_user['forceremember']) === 0)
						{
							$logi_by_remember = false;
							// no login by remember
						}
						elseif(intval($load_user['forceremember']) === 1)
						{
							$logi_by_remember = true;

						}
					}
				}

				if($logi_by_remember)
				{
					// login accessed and user must be login
					self::init($user_id);

					if(isset($_SESSION['main_account']))
					{
						// if the admin user login by this user
						// not save the session
					}
					else
					{
						\dash\db\sessions::set($user_id);
						\dash\log::set('userLoginByRemember');
					}
				}
				else
				{
					// code find and not use to login
					// then we terminate this code
					\dash\db\sessions::terminate_cookie();
				}
			}
			else
			{
				// no user id found from this cookie
				// delete it
				\dash\db\sessions::terminate_cookie();
			}

		}
		else
		{
			// check session is not deactive
			$cookie = \dash\db\sessions::get_cookie();

			if(!$cookie)
			{
				return;
			}

			if(isset($_SESSION['main_account']))
			{
				// if the admin user login by this user
				// not save the session
			}
			else
			{
				$status = \dash\db\sessions::is_active($cookie, \dash\user::id());

				if($status === false)
				{

					\dash\db\sessions::disable_cookie($cookie, \dash\user::id());

					\dash\db\sessions::terminate_cookie();

					\dash\log::set('userForceLogoutAuto');
					// muset force logout this user
					\dash\utility\enter::set_logout(\dash\user::id());
				}
			}
		}
	}
}
?>
