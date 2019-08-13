<?php
namespace content_enter\pass\set;


class model
{

	public static function post()
	{
		if(\dash\request::post('ramzNew'))
		{
			$temp_ramz = \dash\request::post('ramzNew');

			if(isset($_POST['ramzNew']))
			{
				$temp_ramz = $_POST['ramzNew'];
			}
			// check min and max of password and make error
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
				\dash\log::set('creazyPassword');

				// creazy password
				return false;
			}
		}
		else
		{
			\dash\utility\enter::try('pass_set_password_not_set');
			\dash\notif::error(T_("No password was send"));
			return false;
		}

		\dash\log::set('setPasswordRequest');

		// set session verify_from set
		\dash\utility\enter::set_session('verify_from', 'set');

		// send code way
		\dash\utility\enter::go_to_verify();
	}
}
?>