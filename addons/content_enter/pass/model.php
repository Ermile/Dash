<?php
namespace content_enter\pass;


class model
{

	public static function post()
	{
		if(!\dash\request::post('ramz'))
		{
			\dash\notif::error(T_("Please enter your password"));
			return false;
		}

		$ramz = \dash\request::post('ramz');

		if(\dash\utility\enter::user_data('password'))
		{
			// the password is okay
			if(\dash\utility::hasher($ramz, \dash\utility\enter::user_data('password')))
			{
				\dash\log::set('userLogin');

				\dash\utility\enter::enter_set_login();
				\dash\utility\enter::next_step('okay');
				// set login session
				\dash\utility\enter::go_to('okay');
			}
			else
			{
				\dash\log::set('invalidPassword');
				// wrong password sleep code
				\dash\utility\enter::try('pass_invalid_pass');
				\dash\code::sleep(3);
				\dash\notif::error(T_("Invalid password, try again"));
				return false;
			}
		}
		else
		{
			\dash\utility\enter::go_to('error');
		}
	}
}
?>