<?php
namespace content_enter\pass\change;


class model extends \addons\content_enter\pass\model
{

	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_pass($_args)
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

		// check old password is okay
		if(!\dash\utility::hasher(\dash\request::post('ramz'), \dash\user::login('pass')))
		{
			self::plus_try_session('change_password_invalid_old');
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
		\dash\utility\enter::set_session('verify_from', 'change');
		// find send way to send code
		// and send code

		// send code way
		\dash\utility\enter::go_to_verify();
	}
}
?>