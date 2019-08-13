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
			\dash\log::set('userHitSignup3>30m');

			\dash\notif::warn(T_("You do not have permission to register for up to ten minutes"). ":)");
			return false;
		}

		if(\dash\request::post('password'))
		{
			\dash\log::set('hiddenPasswordFieldIsFull');
			\dash\notif::warn(T_("Your browser has sent a saved password. Delete it and continue"));
			$get          = \dash\request::get();
			$get['clean'] = '1';
			\dash\redirect::to(\dash\url::this(). '?'. http_build_query($get));
			return false;
		}

		// clean existing session
		\dash\utility\enter::clean_session();


		$mobile = \dash\request::post('mobile');
		if(!$mobile)
		{
			\dash\notif::error(T_("Pleaes set mobile number"), 'mobile');
			return false;
		}

		$mobile = \dash\utility\filter::mobile($mobile);
		if(!$mobile)
		{
			\dash\notif::error(T_("Pleaes set a valid mobile number"), 'mobile');
			return false;
		}

		$username = \dash\request::post('username');
		if(\dash\option::config('enter', 'singup_username'))
		{
			if(!$username || mb_strlen($username) < 5 || mb_strlen($username) > 50 )
			{
				\dash\notif::error(T_("Pleaes set a valid username"), 'username');
				return false;
			}
		}
		else
		{
			$username = null;
		}

		if(\dash\option::config('enter', 'singup_username') && !preg_match("/[A-Za-z0-9\_]/", $username))
		{
			\dash\log::set('usernameInvalidSyntax', ['code' => $username]);
			\dash\notif::error(T_("Username must in [A-Za-z0-9]"), 'username');
			return false;
		}

		$ramz = \dash\request::post('ramzNew');
		if(isset($_POST['ramzNew']))
		{
			$ramz = $_POST['ramzNew'];
		}

		if(!$ramz || mb_strlen($ramz) < 5 || mb_strlen($ramz) > 50)
		{
			\dash\notif::error(T_("Pleaes set a valid password"), 'ramzNew');
			return false;
		}

		if($username && $ramz && $username === $ramz)
		{
			\dash\notif::error(T_("Please do not use your username as password. Never Ever!"), ['element' => ['ramzNew', 'username']]);
			return false;
		}

		if($mobile && strpos($ramz, $mobile) !== false)
		{
			\dash\notif::error(T_("Please do not use your mobile in password!"), ['element' => ['ramzNew', 'mobile']]);
			return false;
		}

		$displayname = \dash\request::post('displayname');
		if(!$displayname || mb_strlen($displayname) > 50)
		{
			\dash\notif::error(T_("Invalid full name"), 'displayname');
			return false;
		}

		if(\dash\option::config('enter', 'singup_username'))
		{
			$check_username = \dash\db\users::get_by_username($username);
			if($check_username)
			{
				\dash\log::set('usernameTaken', ['username' => $username]);
				\dash\notif::error(T_("This username is already taken."), 'username');
				return false;
			}
		}

		$check_mobile = \dash\db\users::get_by_mobile($mobile);
		if($check_mobile)
		{
			\dash\log::set('mobileExistTryToSignup');
			\dash\notif::error(T_("This mobile is already signuped. You can login by this mobile"), 'mobile');
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

	}
}
?>