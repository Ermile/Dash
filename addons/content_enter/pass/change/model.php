<?php
namespace content_enter\pass\change;


class model
{

	public static function post()
	{
		// check ramz fill
		if(!\dash\request::post('ramz'))
		{
			\dash\notif::error(T_("Please fill the password field"));
			return false;
		}

		// check ramz fill
		if(!\dash\request::post('ramzNew'))
		{
			\dash\notif::error(T_("Please fill the new password field"));
			return false;
		}

		// check old pass == new pass?
		if(\dash\request::post('ramz') == \dash\request::post('ramzNew'))
		{
			\dash\notif::error(T_("Please set a different password!"));
			return false;
		}

		// check min and max password
		if(!\dash\utility\enter::check_pass_syntax(\dash\request::post('ramz')))
		{
			return false;
		}

		// check min and max password
		if(!\dash\utility\enter::check_pass_syntax(\dash\request::post('ramzNew')))
		{
			return false;
		}

		if(!\dash\user::detail('password'))
		{
			\dash\utility\enter::try('change_pass_have_not_pass');
			\dash\notif::error(T_("You do not have any password!"). ' '. T_("Please logout and login again."));
			return false;
		}

		// check old password is okay
		if(!\dash\utility::hasher(\dash\request::post('ramz'), \dash\user::detail('password')))
		{
			\dash\utility\enter::try('change_pass_invalid_old_pass');
			\dash\code::sleep(3);
			\dash\notif::error(T_("Invalid old password"));
			return false;
		}

		// hesh ramz to find is this ramz is easy or no
		// creazy password !
		$temp_ramz_hash = \dash\utility::hasher(\dash\request::post('ramzNew'));
		// if \dash\notif status continue
		if(\dash\engine\process::status())
		{
			\dash\utility\enter::set_session('temp_ramz', \dash\request::post('ramzNew'));
			\dash\utility\enter::set_session('temp_ramz_hash', $temp_ramz_hash);
		}
		else
		{
			// creazy password
			return false;
		}

		// set session verify_from change
		\dash\utility\enter::set_session('verify_from', 'password_change');
		// find send way to send code
		// and send code

		// send code way
		\dash\utility\enter::go_to_verify();
	}
}
?>