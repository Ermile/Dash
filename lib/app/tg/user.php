<?php
namespace dash\app\tg;


class user
{
	private static $user_detail = [];

	/**
	* for test, remove user completely
	*/
	public static function hard_delete($_chat_id)
	{
		$user_detail = self::get($_chat_id);
		$result = null;
		if(isset($user_detail['id']))
		{
			$result = \dash\db\user_telegram::hard_delete($user_detail['id']);
		}

		return $result;
	}

	public static function set($_chat_id)
	{
		if(\dash\user::id() && $_chat_id)
		{
			\dash\app\user_telegram::add(['user_id' => \dash\user::id(), 'chatid' => $_chat_id]);
		}
	}

	// get user detail by chatid in user_telegram
	public static function get($_chat_id)
	{
		if(!$_chat_id)
		{
			return null;
		}

		$get = \dash\db\user_telegram::get(['chatid' => $_chat_id, 'limit' => 1]);

		if(!$get)
		{
			return null;
		}

		return $get;
	}


	// login user
	public static function init($_user_id)
	{
		if($_user_id)
		{
			return \dash\user::init($_user_id);
		}
	}


	// add new user in users table and user_telegram table
	public static function add($_args, $_option = [])
	{
		$_args['force_add'] = true;

		$myStatus   = 'active';
		$myUsername = null;
		$myChatid   = null;

		if(isset($_args['status']))
		{
			$myStatus = $_args['status'];
		}

		if(isset($_args['username']))
		{
			$myUsername = $_args['username'];
		}

		if(isset($_args['chatid']))
		{
			$myChatid = $_args['chatid'];
		}

		unset($_args['status']);
		unset($_args['username']);
		unset($_args['chatid']);

		$result             = \dash\app\user::add($_args, ['force_add' => true, 'encode' => false]);

		$_args['status']   = $myStatus;
		$_args['username'] = $myUsername;
		$_args['chatid']   = $myChatid;

		if(isset($result['user_id']))
		{
			$_args['user_id'] = $result['user_id'];
			\dash\app\user_telegram::add($_args);
		}
		else
		{
			\dash\log::set('usersSignupUserTelegramErrorSignup');
		}

		return $result;
	}

	// return chatid from hook
	private static function chatid()
	{
		$chatid = \dash\social\telegram\hook::from();
		return $chatid;
	}


	// get detail of user in user_telegram
	public static function detail($_key = null)
	{
		$chatid  = self::chatid();
		$user_id = \dash\user::id();

		if($chatid && $user_id && empty(self::$user_detail))
		{
			$load = \dash\db\user_telegram::get(['chatid' => $chatid, 'user_id' => $user_id, 'limit' => 1]);
			if($load)
			{
				self::$user_detail = $load;
			}
		}

		if($_key)
		{
			if(array_key_exists($_key, self::$user_detail))
			{
				return self::$user_detail[$_key];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$user_detail;
		}
	}

	// update user detail in user_telegram
	public static function update($_args)
	{
		if(!empty($_args) && is_array($_args) && self::detail('id'))
		{
			\dash\db\user_telegram::update($_args, self::detail('id'));
		}
	}


	// get and set user language in user_telegram
	public static function lang($_lang = null)
	{
		if(!$_lang)
		{
			return self::detail('language');
		}
		else
		{

			if(mb_strlen($_lang) === 2)
			{
				$update = self::update(['language' => $_lang]);

				if($update)
				{
					\dash\app\tg\account::relogin();
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		return false;
	}


	// get and set user status in user_telegram
	public static function status($_status = null)
	{
		if(!$_status)
		{
			return self::detail('status');
		}
		else
		{
			$update = self::update(['status' => $_status]);
			if($update)
			{
				\dash\app\tg\account::relogin();
				return true;
			}
			else
			{
				return false;
			}
		}
		return false;
	}

	// get and set user username in user_telegram
	public static function username($_username = null)
	{
		if(!$_username)
		{
			return self::detail('username');
		}
		else
		{
			$update = self::update(['username' => $_username]);
			if($update)
			{
				\dash\app\tg\account::relogin();
				return true;
			}
			else
			{
				return false;
			}
		}
		return false;
	}


	// get and set user lastupdate in user_telegram
	public static function tgUpdateActivityTime($_set = null)
	{
		if(!$_set)
		{
			return self::detail('lastupdate');
		}
		else
		{
			$update = self::update(['lastupdate' => date("Y-m-d H:i:s")]);
			if($update)
			{
				// \dash\app\tg\account::relogin();
				return true;
			}
			else
			{
				return false;
			}
		}
		return false;
	}


	// return user_id
	public static function id()
	{
		return \dash\user::id();
	}


}
?>