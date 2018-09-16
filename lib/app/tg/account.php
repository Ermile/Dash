<?php
namespace dash\app\tg;


class account
{

	public static function register($_chat_id, $_mobile, $_arg = [])
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
			return true;
		}

		if(!is_numeric($_chat_id))
		{
			// invalid chatid
			return false;
		}

		$mobile_exist = \dash\db\users::get_by_mobile($mobile);

		if(!$mobile_exist || !isset($mobile_exist['id']))
		{
			\dash\db\users::update(['mobile' => $mobile], \dash\user::id());
			// to loadmobile and some other field
			self::relogin_user();

			return true;
		}
		else
		{
			// update chatid
			// set old chatid in meta if exist
			// remove chatid from current user logined
			// disable current user and remove  chatid and mobile
			// relogin user

			$update_new_user               = [];
			$update_new_user['chatid']     = $_chat_id;

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

				$meta['old_chatid'] = \dash\user::detail('chatid');

				$update_new_user['meta']  = json_encode($meta, JSON_UNESCAPED_UNICODE);
			}


			$update_current_user           = [];
			$update_current_user['chatid'] = null;
			$update_current_user['mobile'] = null;
			$update_current_user['status'] = 'unreachable';

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

				$meta['removed_chatid'] = \dash\user::detail('chatid');

				$update_current_user['meta'] = json_encode($meta, JSON_UNESCAPED_UNICODE);
			}

			\dash\db\users::update($update_current_user, \dash\user::id());
			\dash\db\users::update($update_new_user, $mobile_exist['id']);
			self::relogin_user($mobile_exist['id']);
			return true;
		}
	}


	private static function relogin_user($_user_id = null)
	{
		if(!$_user_id)
		{
			$user_id   = \dash\user::id();
		}
		else
		{
			$user_id = $_user_id;
		}

		$user_code = \dash\coding::encode($user_id);
		\dash\user::destroy();
		\dash\user::init_tg($user_code);
	}
}
?>