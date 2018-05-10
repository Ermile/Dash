<?php
namespace content_enter\home;


class model
{
	public static function login_another_session()
	{
		if(\dash\permission::supervisor())
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

				\dash\user::destroy();

				\dash\utility\enter::load_user_data($user_id, 'user_id');

				$_SESSION['main_account'] = $main_account;
				$_SESSION['main_mobile']  = $main_mobile;

				// set login session
				$redirect_url = \dash\utility\enter::enter_set_login(null, true);
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
		/**
		 * check login by another session
		 */
		if(self::login_another_session())
		{
			return;
		}

		$count = \dash\session::get('enter_session_check');
		if($count)
		{
			\dash\session::set('enter_session_check', $count + 1, null, 60 * 3);
		}
		else
		{
			\dash\session::set('enter_session_check', 1, null, 60 * 3);
		}

		$anotherPerm = \dash\permission::supervisor();
		if($count >= 3 && !$anotherPerm)
		{
			\dash\notif::error(T_("You hit our maximum try limit."). ' '. T_("Try again later!"));
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


		// if old mobile is different by new mobile
		// save in session this user change the mobile
		if($old_usernameormobile != $usernameormobile)
		{
			\dash\utility\enter::try('diffrent_mobile');
			\dash\utility\enter::set_session('diffrent_mobile', intval(\dash\utility\enter::get_session('diffrent_mobile')) + 1);
		}

		// set posted mobile in SESSION
		\dash\utility\enter::set_session('usernameormobile', $usernameormobile);

		// load user data by mobile
		$user_data = \dash\utility\enter::load_user_data($usernameormobile, 'usernameormobile');

		// the user not found must be signup
		if(!$user_data)
		{
			\dash\utility\enter::try('login_user_not_found');
			\dash\notif::error(T_("Username not found"));
			return false;
		}

		// if this user is blocked or filtered go to block page
		if(in_array(\dash\utility\enter::user_data('status'), ['filter', 'block']))
		{
			\dash\utility\enter::try('login_status_block');
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
				\dash\utility\enter::try('browser_pass_saved_invalid');
				$get = \dash\request::get();
				$get['clean'] = '1';
				\dash\notif::warn(T_("Opts!, Maybe your browser saved your password incorrectly."). ' '. T_("Try again!"));
				\dash\redirect::to(\dash\url::this(). '?'. http_build_query($get));
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