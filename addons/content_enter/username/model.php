<?php
namespace content_enter\username;


class model
{
	public  static function post()
	{
		if(!\dash\request::post('password'))
		{
			\dash\notif::error(T_("Your browser has sent a saved password. Delete it and continue"));
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
		\dash\utility\enter::clean_session();

		// load userdata by username
		\dash\utility\enter::load_user_data($username, 'username');

		// save username in $_SESSION['username']['temp_username']
		\dash\utility\enter::set_session('username', 'temp_username', $username);

		// check username exist
		if(!\dash\utility\enter::user_data() || !\dash\utility\enter::user_data('id'))
		{
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
				\dash\utility\enter::set_session('mobile', \dash\utility\enter::user_data('mobile'));
			}

			// set step session
			\dash\utility\enter::set_step_session('username', true);

			// open this pages after this page
			\dash\utility\enter::next_step('pass');
			// open lock pass/recovery to load it
			\dash\utility\enter::open_lock('pass/recovery');

			// go to pass page
			\dash\utility\enter::go_to('pass');
		}

	}
}
?>