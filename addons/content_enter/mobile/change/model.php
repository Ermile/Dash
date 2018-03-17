<?php
namespace addons\content_enter\pass\change;


class model extends \addons\content_enter\pass\model
{

	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_pass($_args)
	{
		// check inup is ok
		if(!self::check_input('pass/change'))
		{
			\lib\notif::error(T_("Dont!"));
			return false;
		}

		// check ramz fill
		if(!\lib\request::post('ramz'))
		{
			\lib\notif::error(T_("Please fill the password field"));
			return false;
		}

		// check ramz fill
		if(!\lib\request::post('ramzNew'))
		{
			\lib\notif::error(T_("Please fill the new password field"));
			return false;
		}

		// check old pass == new pass?
		if(\lib\request::post('ramz') == \lib\request::post('ramzNew'))
		{
			\lib\notif::error(T_("Please set a different password!"));
			return false;
		}

		// check min and max password
		if(!$this->check_pass_syntax(\lib\request::post('ramz')))
		{
			return false;
		}

		// check min and max password
		if(!$this->check_pass_syntax(\lib\request::post('ramzNew')))
		{
			return false;
		}

		// check old password is okay
		if(!\lib\utility::hasher(\lib\request::post('ramz'), \lib\user::login('pass')))
		{
			self::plus_try_session('change_password_invalid_old');
			\lib\notif::error(T_("Invalid old password"));
			return false;
		}

		// hesh ramz to find is this ramz is easy or no
		// creazy password !
		$temp_ramz_hash = \lib\utility::hasher(\lib\request::post('ramzNew'));
		// if \lib\notif status continue
		if(\lib\notif::$status)
		{
			self::set_enter_session('temp_ramz', \lib\request::post('ramzNew'));
			self::set_enter_session('temp_ramz_hash', $temp_ramz_hash);
		}
		else
		{
			// creazy password
			return false;
		}

		// set session verify_from change
		self::set_enter_session('verify_from', 'change');
		// find send way to send code
		// and send code

		// send code way
		self::send_code_way();
	}
}
?>