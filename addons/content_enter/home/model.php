<?php
namespace content_enter\home;


class model
{
	public static function login_another_session()
	{
		if(\dash\permission::access('enter:another:session'))
		{
			$user_id = null;

			if(\dash\request::post('usernameormobile') !== \dash\user::login('mobile') && !\dash\request::get('userid'))
			{
				$user_data = \dash\db\users::get_by_mobile(\dash\utility\filter::mobile(\dash\request::post('usernameormobile')));

				if(isset($user_data['id']))
				{
					$user_id = $user_data['id'];
				}
				else
				{
					\dash\notif::error(T_("Mobile not found"));
					return false;
				}
			}

			if(!$user_id && \dash\request::get('userid'))
			{
				$user_id = \dash\request::get('userid');
			}

			if($user_id)
			{

				$main_account = \dash\user::id();
				$main_mobile  = \dash\user::login('mobile');

				if(!\dash\db\users::get_by_id($user_id))
				{
					\dash\notif::error(T_("User not found"));
					return false;
				}

				// clean existing session
				\dash\utility\enter::clean_session();
				unset($_SESSION['user']);
				unset($_SESSION['permission']);

				\dash\utility\enter::$user_id = $user_id;
				\dash\utility\enter::load_user_data($user_id, 'user_id');

				$_SESSION['main_account'] = $main_account;
				$_SESSION['main_mobile']  = $main_mobile;

				// set login session
				$redirect_url = \dash\utility\enter::enter_set_login();
				// save redirect url in session to get from okay page
				\dash\utility\enter::set_session('redirect_url', $redirect_url);
				// set okay as next step
				\dash\utility\enter::next_step('okay');
				// go to okay page
				\dash\utility\enter::go_to('okay');
				return true;
			}
			return false;
		}
		return false;
	}



	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function post()
	{
		$count = \dash\session::get('enter_session_check');
		if($count)
		{
			\dash\session::set('enter_session_check', $count + 1, null, 60 * 3);
		}
		else
		{
			\dash\session::set('enter_session_check', 1, null, 60 * 3);
		}

		$anotherPerm = \dash\permission::access('enter:another:session');
		if($count >= 3 && !$anotherPerm)
		{
			\dash\notif::error(T_("How are you?"). ":)");
			return false;
		}

		// get saved mobile in session to find count of change mobile
		$old_usernameormobile = \dash\utility\enter::get_session('usernameormobile');

		// clean existing session
		\dash\utility\enter::clean_session();

		$password         = \dash\request::post('password');
		$usernameormobile = \dash\request::post('usernameormobile');

		if(!$usernameormobile)
		{
			\dash\notif::error(T_("Please set the username or mobile or email"));
			return false;
		}

		/**
		 * check login by another session
		 */
		if(self::login_another_session())
		{
			return;
		}

		// if old mobile is different by new mobile
		// save in session this user change the mobile
		if($old_usernameormobile != $usernameormobile)
		{
			\dash\utility\enter::set_session('diffrent_mobile', intval(\dash\utility\enter::get_session('diffrent_mobile')) + 1);
		}

		// set posted mobile in SESSION
		\dash\utility\enter::set_session('usernameormobile', $usernameormobile);

		// load user data by mobile
		$user_data = \dash\utility\enter::load_user_data($usernameormobile, 'usernameormobile');

		// the user not found must be signup
		if(!$user_data)
		{
			\dash\notif::error(T_("Username not found"));
			return false;
		}

		// if this user is blocked or filtered go to block page
		if(in_array(\dash\utility\enter::user_data('status'), ['filter', 'block']))
		{
			// block page
			\dash\utility\enter::next_step('block');
			// go to block page
			\dash\utility\enter::go_to('block');
			return;
		}

		// the password field is empty
		if(!\dash\utility\enter::user_data('password'))
		{
			// lock all step and set just this page to load
			\dash\utility\enter::open_lock('pass/set');
			// open lock pass/recovery
			\dash\utility\enter::open_lock('pass/recovery');
			// go to pass to check password
			\dash\utility\enter::go_to('pass/set');
		}

		if($password)
		{
			if(\dash\utility::hasher($password, \dash\utility\enter::user_data('password')))
			{
				// login
				// the browser was saved the password
				\dash\utility\enter::enter_set_login(null, true);
				return ;
			}
			else
			{
				\dash\notif::warn(T_("Opts!, Maybe your browser saved your password incorrectly."). ' '. T_("Please remove your saved password and try again"));
				return false;
			}
		}
		else
		{
			// lock all step and set just this page to load
			\dash\utility\enter::next_step('pass');
			// open lock pass/recovery
			\dash\utility\enter::open_lock('pass/recovery');
			// go to pass to check password
			\dash\utility\enter::go_to('pass');
		}

	}
}
?>