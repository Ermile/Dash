<?php
namespace content_enter\signup;


class model
{

	public static function post()
	{

		$count = \dash\session::get('count_signup_check');
		if($count)
		{
			\dash\session::set('count_signup_check', $count + 1, null, 60 * 10);
		}
		else
		{
			\dash\session::set('count_signup_check', 1, null, 60 * 10);
		}

		if($count >= 3)
		{
			\dash\log::db('userHitSignup3>30m');

			\dash\notif::warn(T_("You do not have permission to register for up to ten minutes"). ":)");
			return false;
		}

		if(\dash\request::post('password'))
		{
			\dash\log::db('hiddenPasswordFieldIsFull');
			\dash\notif::warn(T_("Your browser has sent a saved password. Delete it and continue"));
			$get          = \dash\request::get();
			$get['clean'] = '1';
			\dash\redirect::to(\dash\url::this(). '?'. http_build_query($get));
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
			\dash\log::db('usernameInvalidSyntax', ['data' => $username]);
			\dash\notif::error(T_("Username must in [A-Za-z0-9]"));
			return false;
		}

		$ramz = \dash\request::post('ramzNew');
		if(!$ramz || mb_strlen($ramz) < 5 || mb_strlen($ramz) > 50)
		{
			\dash\notif::error(T_("Pleaes set a valid password"));
			return false;
		}

		if($username && $ramz && $username === $ramz)
		{
			\dash\notif::error(T_("Please do not use your username as password. Never Ever!"));
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
				\dash\log::db('usernameTaken', ['data' => $username]);
				\dash\notif::error(T_("This username is already taken."));
				return false;
			}
		}

		$check_mobile = \dash\db\users::get_by_mobile($mobile);
		if($check_mobile)
		{
			\dash\log::db('mobileExistTryToSignup');
			\dash\notif::error(T_("This mobile is already signuped. You can login by this mobile"));
			return false;
		}

		$signup =
		[
			'mobile'      => $mobile,
			'displayname' => $displayname,
			'username'    => $username,
			'status'      => 'awaiting'
		];

		\dash\utility\enter::set_session('verify_from', 'signup');

		\dash\utility\enter::set_session('temp_ramz_hash', \dash\utility::hasher($ramz));

		\dash\utility\enter::set_session('usernameormobile', $mobile);

		\dash\utility\enter::set_session('signup_detail', $signup);

		\dash\utility\enter::go_to_verify();

		// if(!\dash\engine\process::status())
		// {
		// 	return false;
		// }

		// $user_id = \dash\db\users::signup_quick($signup);

		// if(!$user_id)
		// {
		// 	\dash\log::db('userCanNotSignupDB');
		// 	\dash\notif::error(T_("We can not signup you"));
		// 	return false;
		// }
		// \dash\log::db('userSignup');

		// \dash\utility\enter::load_user_data($user_id, 'user_id');
		// \dash\utility\enter::enter_set_login(null, true);
	}
}
?>