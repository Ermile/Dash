<?php
namespace addons\content_enter\pass;


class model extends \addons\content_enter\main\model
{
	public function post_check()
	{
		if(!\dash\request::post('ramz'))
		{
			// plus count empty password
			self::plus_try_session('empty_password');

			\dash\notif::error(T_("Please enter your password"));
			return false;
		}

		// get ramz
		$ramz = \dash\request::post('ramz');

		if(\dash\utility\enter::user_data('password'))
		{
			// the password is okay
			if(\dash\utility::hasher($ramz, \dash\utility\enter::user_data('password')))
			{
				// if the user set two step verification send code
				if(\dash\utility\enter::user_data('twostep'))
				{
					\dash\utility\enter::session_set('verify_from', 'two_step');
					// find way and redirect to it
					self::send_code_way();
					return;
				}
				else
				{
					self::enter_set_login();
					self::next_step('okay');
					// set login session
					self::go_to('okay');
				}
			}
			else
			{
				// plus count invalid password
				self::plus_try_session('invalid_password');

				// wrong password sleep code
				self::sleep_code();

				\dash\notif::error(T_("Invalid password, try again"));
				return false;
			}
		}
		else
		{
			self::go_to('error');
		}
	}


	/**
	 * check password syntax
	 * min
	 * max
	 *
	 * @param      <type>  $_password  The password
	 */
	public function check_pass_syntax($_password, $_debug = true)
	{
		// cehck min leng of password is 6 character
		if(mb_strlen($_password) < 6)
		{
			if($_debug)
			{
				\dash\notif::error(T_("You must set 6 character and large in password"));
			}
			return false;
		}

		// cehck max length of password
		if(mb_strlen($_password) > 99)
		{
			if($_debug)
			{
				\dash\notif::error(T_("You must set less than 99 character in password"));
			}
			return false;
		}

		// no problem ;)
		return true;
	}
}
?>