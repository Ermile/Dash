<?php
namespace addons\content_enter\pass\recovery;


class model extends \addons\content_enter\pass\model
{
	/**
	 * Gets the enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_pass($_args)
	{

	}


	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_pass($_args)
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
					$url = self::enter_set_login();
					// set alert text
					self::set_alert(T_("Your new password is your old password"));
					// set alert link
					self::set_alert_link($url);
					// set alert button caption
					self::set_alert_button(T_("Enter"));
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
			if(!$this->check_pass_syntax($temp_ramz))
			{
				return false;
			}

			// hesh ramz to find is this ramz is easy or no
			// creazy password !
			$temp_ramz_hash = \dash\utility::hasher($temp_ramz);
			// if debug status continue
			if(\dash\engine\process::status())
			{
				\dash\utility\enter::session_set('temp_ramz', $temp_ramz);
				\dash\utility\enter::session_set('temp_ramz_hash', $temp_ramz_hash);
			}
			else
			{
				// creazy password
				return false;
			}
		}
		else
		{
			// plus count invalid password
			self::plus_try_session('no_password_send_verify');

			\dash\notif::error(T_("Invalid Password"));
			return false;
		}

		// set session verify_from recovery
		\dash\utility\enter::session_set('verify_from', 'recovery');
		// find send way to send code
		// and send code
		// set step pass is done
		self::set_step_session('pass', true);

		// send code way
		\dash\utility\enter::send_code_way();
	}
}
?>