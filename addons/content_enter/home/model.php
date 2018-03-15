<?php
namespace addons\content_enter\home;


class model extends \addons\content_enter\main\model
{
	public function login_another_session()
	{
		if(\lib\permission::access('enter:another:session'))
		{
			$user_id = null;

			if(\lib\request::post('usernameormobile') !== $this->login('mobile') && !\lib\utility::get('userid'))
			{
				$user_data = \lib\db\users::get_by_mobile(\lib\utility\filter::mobile(\lib\request::post('usernameormobile')));

				if(isset($user_data['id']))
				{
					$user_id = $user_data['id'];
				}
				else
				{
					\lib\debug::error(T_("Mobile not found"));
					return false;
				}
			}

			if(!$user_id && \lib\utility::get('userid'))
			{
				$user_id = \lib\utility::get('userid');
			}

			if($user_id)
			{

				$main_account = $this->login('id');
				$main_mobile  = $this->login('mobile');

				if(!\lib\db\users::get_by_id($user_id))
				{
					\lib\debug::error(T_("User not found"));
					return false;
				}

				// clean existing session
				self::clean_session();
				unset($_SESSION['user']);
				unset($_SESSION['permission']);

				self::$user_id = $user_id;
				self::load_user_data('user_id');

				$_SESSION['main_account'] = $main_account;
				$_SESSION['main_mobile']  = $main_mobile;

				// set login session
				$redirect_url = self::enter_set_login();
				// save redirect url in session to get from okay page
				self::set_enter_session('redirect_url', $redirect_url);
				// set okay as next step
				self::next_step('okay');
				// go to okay page
				self::go_to('okay');
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
	public function post_enter($_args)
	{
		$count = \lib\session::get('enter_session_check');
		if($count)
		{
			\lib\session::set('enter_session_check', $count + 1, null, 60 * 3);
		}
		else
		{
			\lib\session::set('enter_session_check', 1, null, 60 * 3);
		}

		$anotherPerm = \lib\permission::access('enter:another:session');
		if($count >= 3 && !$anotherPerm)
		{
			\lib\debug::error(T_("How are you?"). ":)");
			return false;
		}

		// get saved mobile in session to find count of change mobile
		$old_usernameormobile = self::get_enter_session('usernameormobile');

		// clean existing session
		self::clean_session();

		$password = \lib\request::post('password');

		/**
		 * check login by another session
		 */
		if($this->login_another_session())
		{
			return;
		}

		$usernameormobile       = \lib\request::post('usernameormobile');
		self::$usernameormobile = $usernameormobile;

		// if old mobile is different by new mobile
		// save in session this user change the mobile
		if($old_usernameormobile && self::$mobile != $old_usernameormobile)
		{
			self::plus_try_session('diffrent_mobile');
		}

		// set posted mobile in SESSION
		self::set_enter_session('usernameormobile', self::$usernameormobile);

		// load user data by mobile
		$user_data = self::load_user_data('usernameormobile');

		// set this step is done
		self::set_step_session('usernameormobile', true);

		// the user not found must be signup
		if(!$user_data)
		{
			\lib\debug::error(T_("Username not found"));
			return false;
		}

		// if this user is blocked or filtered go to block page
		if(in_array(self::user_data('status'), self::$block_status))
		{
			// block page
			self::next_step('block');
			// go to block page
			self::go_to('block');
			return;
		}

		// the password field is empty
		if(!self::user_data('password'))
		{
			// lock all step and set just this page to load
			self::open_lock('pass/set');
			// open lock pass/recovery
			self::open_lock('pass/recovery');
			// go to pass to check password
			self::go_to('pass/set');
		}

		if($password)
		{
			if(\lib\utility::hasher($password, self::user_data('password')))
			{
				// login
				// the browser was saved the password
				self::enter_set_login(null, true);
				return ;
			}
			else
			{
				\lib\debug::warn(T_("Opts!, Maybe your browser saved your password incorrectly."). ' '. T_("Please remove your saved password and try again"));
				return false;
			}
		}
		else
		{
			// lock all step and set just this page to load
			self::next_step('pass');
			// open lock pass/recovery
			self::open_lock('pass/recovery');
			// go to pass to check password
			self::go_to('pass');
		}

	}
}
?>