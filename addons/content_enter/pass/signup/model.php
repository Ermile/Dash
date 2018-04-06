<?php
namespace addons\content_enter\pass\signup;


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

			// check min and max of password and make error
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
			self::plus_try_session('no_password_send_signup');

			\dash\notif::error(T_("No password was send"));
			return false;
		}

		// set session verify_from signup
		\dash\utility\enter::session_set('verify_from', 'signup');
		// find send way to send code
		// and send code
		// set step pass is done
		self::set_step_session('pass', true);

		// send code way
		\dash\utility\enter::send_code_way();
	}
}
?>