<?php
namespace dash\app\tg;


class account
{

	public static function register($_chat_id, $_mobile, $_args = [])
	{
		$mobile = \dash\utility\filter::mobile($_mobile);
		if(!$mobile)
		{
			// invalid mobile synax
			return false;
		}

		if($mobile === \dash\user::detail('mobile'))
		{
			// the user register before and needless to run this code again
			return null;
		}

		if(!is_numeric($_chat_id))
		{
			// invalid chatid
			return false;
		}

		$mobile_exist = \dash\db\users::get_by_mobile($mobile);

		if(!$mobile_exist || !isset($mobile_exist['id']))
		{
			\dash\db\users::update(['mobile' => $mobile], self::id());
			// to loadmobile and some other field
			self::relogin();

			return true;
		}
		else
		{
			// update chatid
			// set old chatid in meta if exist
			// remove chatid from current user logined
			// disable current user and remove  chatid and mobile
			// relogin user

			$update_new_user             = [];
			$update_new_user['chatid']   = $_chat_id;
			$update_new_user['tgstatus'] = 'active';

			if(isset($_args['first_name']) && !isset($mobile_exist['firstname']))
			{
				$update_new_user['firstname'] = substr($_args['first_name'], 0, 90);
			}

			if(isset($_args['last_name']) && !isset($mobile_exist['lastname']))
			{
				$update_new_user['lastname'] = substr($_args['last_name'], 0, 90);
			}

			if(isset($_args['username']) && !isset($mobile_exist['username']))
			{
				$update_new_user['username'] = substr($_args['username'], 0, 90);

				$check_duplicate_username = \dash\db\users::get(['username' => $update_new_user['username'], 'limit' => 1]);
				if($check_duplicate_username)
				{
					if(isset($check_duplicate_username['id']) && intval($check_duplicate_username['id']) === $mobile_exist['id'])
					{
						// no problem
					}
					else
					{
						unset($update_new_user['username']);
					}
				}
			}

			if(!isset($mobile_exist['displayname']) && isset($_args['first_name']) && isset($_args['last_name']))
			{
				$update_new_user['displayname'] = substr($_args['first_name']. ' '. $_args['last_name'], 0, 90);
			}

			$update_new_user = \dash\safe::safe($update_new_user);


			if(\dash\user::detail('language'))
			{
				$update_new_user['language'] = \dash\user::detail('language');
			}

			if(isset($mobile_exist['chatid']))
			{
				$meta = isset($mobile_exist['meta']) ? $mobile_exist['meta'] : null;
				if(is_string($meta))
				{
					$meta = json_decode($meta, true);
				}

				if(!is_array($meta))
				{
					$meta = [];
				}

				if(\dash\user::detail('chatid'))
				{
					$meta['old_chatid'] = \dash\user::detail('chatid');
				}

				if(!empty($meta))
				{
					$update_new_user['meta']  = json_encode($meta, JSON_UNESCAPED_UNICODE);
				}
			}


			$update_current_user             = [];
			$update_current_user['chatid']   = null;
			$update_current_user['mobile']   = null;
			$update_current_user['status']   = 'unreachable';
			$update_current_user['tgstatus'] = 'unreachable';

			if(\dash\user::detail('chatid'))
			{
				$meta = \dash\user::detail('meta');
				if(is_string($meta))
				{
					$meta = json_decode($meta, true);
				}

				if(!is_array($meta))
				{
					$meta = [];
				}

				if(\dash\user::detail('chatid'))
				{
					$meta['removed_chatid'] = \dash\user::detail('chatid');
				}

				if(\dash\user::detail('moible'))
				{
					$meta['removed_mobile'] = \dash\user::detail('moible');
				}

				if(!empty($meta))
				{
					$update_current_user['meta'] = json_encode($meta, JSON_UNESCAPED_UNICODE);
				}
			}

			\dash\db\users::update($update_current_user, self::id());
			\dash\db\users::update($update_new_user, $mobile_exist['id']);

			self::relogin($mobile_exist['id']);
			return true;
		}
	}


	private static function id()
	{
		return \dash\user::id();
		// return \dash\coding::decode(\dash\user::id());
	}


	public static function relogin($_user_id = null)
	{
		if(!$_user_id)
		{
			$user_id   = \dash\user::id();
		}
		else
		{
			$user_id = $_user_id;
			// $user_id = \dash\coding::encode($user_id);
		}

		\dash\user::destroy();
		\dash\app\tg\user::init($user_id);
	}
}
?>