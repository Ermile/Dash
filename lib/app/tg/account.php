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
			if(!\dash\user::detail('mobile'))
			{
				\dash\db\users::update(['mobile' => $mobile], \dash\user::id());
				\dash\db\user_telegram::update_where(['user_id' => \dash\user::id()], ['chatid' => $_chat_id]);
				self::relogin();
			}
			else
			{
				$first_name = isset($_args['first_name']) ? substr($_args['first_name'], 0, 40) : null;
				$last_name  = isset($_args['last_name']) ? substr($_args['last_name'], 0, 40) : null;

				$new_signup =
				[
					'mobile'      => $mobile,
					'firstname'   => $first_name,
					'lastname'    => $last_name,
					'displayname' => trim($first_name. ' '. $last_name),
				];

				$new_user_id = \dash\db\users::signup($new_signup);
				\dash\db\user_telegram::update_where(['user_id' => $new_user_id], ['chatid' => $_chat_id]);
				// to loadmobile and some other field
				self::relogin($new_user_id);
			}

			return true;
		}
		else
		{
			$update_new_user                 = [];

			if(isset($_args['first_name']) && !isset($mobile_exist['firstname']))
			{
				$update_new_user['firstname'] = substr($_args['first_name'], 0, 90);
			}

			if(isset($_args['last_name']) && !isset($mobile_exist['lastname']))
			{
				$update_new_user['lastname'] = substr($_args['last_name'], 0, 90);
			}

			if(!isset($mobile_exist['displayname']) && isset($_args['first_name']) && isset($_args['last_name']))
			{
				$update_new_user['displayname'] = substr($_args['first_name']. ' '. $_args['last_name'], 0, 90);
			}

			if(isset($_args['username']) && !isset($mobile_exist['username']))
			{
				$update_new_user['username']   = substr($_args['username'], 0, 40);

				// $update_new_user['tgusername'] = substr($_args['username'], 0, 40);

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

			$update_user_telegram              = [];
			$update_user_telegram['status']    = 'active';
			$update_user_telegram['user_id']   = $mobile_exist['id'];
			$update_user_telegram['firstname'] = $_args['first_name'];
			$update_user_telegram['lastname']  = $_args['last_name'];
			$update_user_telegram['username']  = $_args['username'];
			$update_user_telegram              = \dash\safe::safe($update_user_telegram);
			\dash\db\user_telegram::update_where($update_user_telegram, ['chatid' => $_chat_id]);


			$update_new_user                   = \dash\safe::safe($update_new_user);
			\dash\db\users::update($update_new_user, $mobile_exist['id']);


			if(!\dash\user::detail('mobile') && !\dash\user::detail('email'))
			{
				$update_current_user             = [];
				$update_current_user['status']   = 'unreachable';
				\dash\db\users::update($update_current_user, \dash\user::id());
			}

			self::relogin($mobile_exist['id']);
			return true;
		}
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
		}

		\dash\user::destroy();
		\dash\app\tg\user::init($user_id);
	}
}
?>