<?php
namespace content_enter\pass\recovery;


class model
{

	public static function post()
	{
		if(\dash\request::post('ramzNew'))
		{
			$temp_ramz = \dash\request::post('ramzNew');

			// check new password = old password
			// needless to change password
			if(\dash\utility\enter::user_data('password'))
			{
				if(\dash\utility::hasher($temp_ramz, \dash\utility\enter::user_data('password')))
				{
					// old pass = new pass
					// aletr to user the new pass = old pass
					// login
					$url             = \dash\utility\enter::enter_set_login();
					$alert           = [];
					$alter['text']   = T_("Your new password is your old password");
					$alter['link']   = $url;
					$alter['button'] = T_("Enter");

					\dash\utility\enter::set_session('alert', $alert);
					// open lock alert page
					\dash\utility\enter::next_step('alert');
					// go to alert page
					\dash\utility\enter::go_to('alert');
					// done ;)
					return;
				}
			}

			// check min and max of password
			// if not okay make debug error and return false
			if(!\dash\utility\enter::check_pass_syntax($temp_ramz))
			{
				return false;
			}

			// hesh ramz to find is this ramz is easy or no
			// creazy password !
			$temp_ramz_hash = \dash\utility::hasher($temp_ramz);
			// if debug status continue
			if(\dash\engine\process::status())
			{
				\dash\utility\enter::set_session('temp_ramz', $temp_ramz);
				\dash\utility\enter::set_session('temp_ramz_hash', $temp_ramz_hash);
			}
			else
			{
				// creazy password
				return false;
			}
		}
		else
		{
			\dash\code::sleep(3);
			\dash\notif::error(T_("Invalid Password"));
			return false;
		}

		// set session verify_from recovery
		\dash\utility\enter::set_session('verify_from', 'recovery');

		// send code way
		\dash\utility\enter::go_to_verify();
	}
}
?>