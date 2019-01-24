<?php
namespace content_enter\username\set;


class model
{

	public function post()
	{
		$username = \dash\request::post('username');
		$username = trim($username);
		if($username)
		{
			if(mb_strlen($username) < 5)
			{
				\dash\notif::error(T_("You must set large than 5 character in username"));
				return false;
			}

			if(mb_strlen($username) > 50)
			{
				\dash\notif::error(T_("You must set less than 50 character in username"));
				return false;
			}

			// check username exist
			$check_exist_name = \dash\db\users::get_by_username($username);

			if(!empty($check_exist_name))
			{
				\dash\log::set('usernameTaken', ['username' => $username]);
				\dash\notif::error(T_("This username alreay taked!"));
				return false;
			}

			\dash\log::set('usernameSet', ['username' => $username]);


			// \dash\db\users::update(['username' => $username], \dash\user::id());
			// set the alert message
			\dash\utility\enter::set_session('alert' , ['text' => T_("Your username was set")]);
			// open lock of alert page
			\dash\utility\enter::next_step('alert');
			// go to alert page
			\dash\utility\enter::go_to('alert');

		}
		else
		{
			\dash\utility\enter::try('username_username_not_set');
			\dash\notif::error(T_("Please enter the username"));
			return false;
		}
	}
}
?>