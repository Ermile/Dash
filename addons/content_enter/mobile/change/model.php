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
			\lib\debug::error(T_("Dont!"));
			return false;
		}

		// check ramz fill
		if(!\lib\utility::post('ramz'))
		{
			\lib\debug::error(T_("Please fill the password field"));
			return false;
		}

		// check ramz fill
		if(!\lib\utility::post('ramzNew'))
		{
			\lib\debug::error(T_("Please fill the new password field"));
			return false;
		}

		// check old pass == new pass?
		if(\lib\utility::post('ramz') == \lib\utility::post('ramzNew'))
		{
			\lib\debug::error(T_("Please set a different password!"));
			return false;
		}

		// check min and max password
		if(!$this->check_pass_syntax(\lib\utility::post('ramz')))
		{
			return false;
		}

		// check min and max password
		if(!$this->check_pass_syntax(\lib\utility::post('ramzNew')))
		{
			return false;
		}

		// check old password is okay
		if(!\lib\utility::hasher(\lib\utility::post('ramz'), $this->login('pass')))
		{
			self::plus_try_session('change_password_invalid_old');
			\lib\debug::error(T_("Invalid old password"));
			return false;
		}

		// hesh ramz to find is this ramz is easy or no
		// creazy password !
		$temp_ramz_hash = \lib\utility::hasher(\lib\utility::post('ramzNew'));
		// if \lib\debug status continue
		if(\lib\debug::$status)
		{
			self::set_enter_session('temp_ramz', \lib\utility::post('ramzNew'));
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