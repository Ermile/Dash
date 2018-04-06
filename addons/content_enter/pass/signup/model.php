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
		if(\lib\request::post('ramzNew'))
		{
			$temp_ramz = \lib\request::post('ramzNew');

			// check min and max of password and make error
			if(!$this->check_pass_syntax($temp_ramz))
			{
				return false;
			}

			// hesh ramz to find is this ramz is easy or no
			// creazy password !
			$temp_ramz_hash = \lib\utility::hasher($temp_ramz);
			// if debug status continue
			if(\lib\engine\process::status())
			{
				self::set_enter_session('temp_ramz', $temp_ramz);
				self::set_enter_session('temp_ramz_hash', $temp_ramz_hash);
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

			\lib\notif::error(T_("No password was send"));
			return false;
		}

		// set session verify_from signup
		self::set_enter_session('verify_from', 'signup');
		// find send way to send code
		// and send code
		// set step pass is done
		self::set_step_session('pass', true);

		// send code way
		self::send_code_way();
	}
}
?>