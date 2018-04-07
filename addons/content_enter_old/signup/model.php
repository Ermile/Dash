<?php
namespace content_enter\signup;


class model extends \addons\content_enter\main\model
{

	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_signup($_args)
	{

		$count = \dash\session::get('count_signup_check');
		if($count)
		{
			\dash\session::set('count_signup_check', $count + 1, null, 60 * 30);
		}
		else
		{
			\dash\session::set('count_signup_check', 1, null, 60 * 30);
		}

		if($count >= 3)
		{
			\dash\notif::warn(T_("How are you?"). ":)");
			return false;
		}

		if(\dash\request::post('password'))
		{
			\dash\notif::error(T_("Dont!"));
			return false;
		}

		$mobile = \dash\request::post('mobile');
		if(!$mobile)
		{
			\dash\notif::error(T_("Pleaes set mobile number"));
			return false;
		}

		$mobile = \dash\utility\filter::mobile($mobile);
		if(!$mobile)
		{
			\dash\notif::error(T_("Pleaes set a valid mobile number"));
			return false;
		}

		$username = \dash\request::post('username');
		if(\dash\option::config('enter', 'singup_username'))
		{
			if(!$username || mb_strlen($username) < 5 || mb_strlen($username) > 50 )
			{
				\dash\notif::error(T_("Pleaes set a valid username"));
				return false;
			}
		}

		if(\dash\option::config('enter', 'singup_username') && !preg_match("/[A-Za-z0-9\_]/", $username))
		{
			\dash\notif::error(T_("Username must in [A-Za-z0-9]"));
			return false;
		}

		$ramz = \dash\request::post('ramzNew');
		if(!$ramz || mb_strlen($ramz) < 5 || mb_strlen($ramz) > 50)
		{
			\dash\notif::error(T_("Pleaes set a valid password"));
			return false;
		}

		$displayname = \dash\request::post('displayname');
		if(!$displayname || mb_strlen($displayname) > 50)
		{
			\dash\notif::error(T_("Invalid full name"));
			return false;
		}

		if(\dash\option::config('enter', 'singup_username'))
		{
			$check_username = \dash\db\users::get_by_username($username);
			if($check_username)
			{
				\dash\notif::error(T_("This username is already taken."));
				return false;
			}
		}

		$check_mobile = \dash\db\users::get_by_mobile($mobile);
		if($check_mobile)
		{
			\dash\notif::error(T_("This mobile is already signuped. You can login by this mobile"));
			return false;
		}

		$signup =
		[
			'mobile'      => $mobile,
			'displayname' => $displayname,
			'password'    => \dash\utility::hasher($ramz),
			'username'    => $username,
			'status'      => 'awaiting'
		];

		if(!\dash\engine\process::status())
		{
			return false;
		}

		$user_id = \dash\db\users::signup_quick($signup);

		if(!$user_id)
		{
			\dash\notif::error(T_("We can not signup you"));
			return false;
		}

		self::$user_id = $user_id;
		self::load_user_data('user_id');
		self::enter_set_login(null, true);

	}
}
?>