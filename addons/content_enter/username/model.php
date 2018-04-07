<?php
namespace content_enter\username;


class model extends \addons\content_enter\main\model
{
	public function post_username()
	{
		if(!\dash\request::post('password'))
		{
			\dash\notif::error(T_("Dont!"));
			return false;
		}
		// get user name
		$username = \dash\request::post('username');
		// check user name is fill
		if(!$username)
		{
			\dash\notif::error(T_("Please fill the username field"), 'username');
			return false;
		}

		// clean session
		self::clean_session();

		// set username
		self::$username = $username;
		// load userdata by username
		self::load_user_data($username, 'username');

		// save username in $_SESSION['username']['temp_username']
		self::set_session('username', 'temp_username', $username);

		// check username exist
		if(!\dash\utility\enter::user_data() || !\dash\utility\enter::user_data('id'))
		{
			self::plus_try_session('invalid_username');

			\dash\notif::error(T_("Username not found"));
			return false;
		}
		elseif(!\dash\utility\enter::user_data('password'))
		{
			// BUG username set and password is not set

			// log meta
			$log_meta =
			[
				'meta' =>
				[
					'session'   => $_SESSION,
					'user_data' => \dash\utility\enter::user_data(),
				],
			];
			\dash\db\logs::set('enter:username:set:password:notset', \dash\utility\enter::user_data('id'), $log_meta);
			// go to mobile
			\dash\utility\enter::go_to('base');
		}
		else
		{
			// user enter by username
			// we need to her mobile to recovery this
			if(!\dash\utility\enter::get_session('mobile') && \dash\utility\enter::user_data('mobile'))
			{
				\dash\utility\enter::session_set('mobile', \dash\utility\enter::user_data('mobile'));
			}

			// set step session
			self::set_step_session('username', true);

			// open this pages after this page
			\dash\utility\enter::next_step('pass');
			// open lock pass/recovery to load it
			self::open_lock('pass/recovery');

			// go to pass page
			\dash\utility\enter::go_to('pass');
		}

	}
}
?>