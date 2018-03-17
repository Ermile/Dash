<?php
namespace addons\content_enter\username\change;


class model extends \addons\content_enter\main\model
{

	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_username($_args)
	{
		// remove username
		if(\lib\request::post('type') === 'remove')
		{
			// set session verify_from username remove
			self::set_enter_session('verify_from', 'username_remove');

			// send code way
			self::send_code_way();

			return;
		}

		if(!\lib\request::post('usernameNew'))
		{
			\lib\notif::error(T_("Plese fill the new username"));
			return false;
		}

		if(mb_strlen(\lib\request::post('usernameNew')) < 5)
		{
			\lib\notif::error(T_("You must set large than 5 character in new username"));
			return false;
		}

		if(mb_strlen(\lib\request::post('usernameNew')) > 50)
		{
			\lib\notif::error(T_("You must set less than 50 character in new username"));
			return false;
		}


		if($this->login('username') == \lib\request::post('usernameNew'))
		{
			\lib\notif::error(T_("Please select a different username"));
			return false;
		}


		// check username exist
		$check_exist_name = \lib\db\users::get_by_username(\lib\request::post('usernameNew'));

		if(!empty($check_exist_name))
		{
			\lib\notif::error(T_("This username alreay taked!"));
			return false;
		}


		if(\lib\request::post('usernameNew'))
		{
			self::set_enter_session('temp_username', \lib\request::post('usernameNew'));
		}

		// set session verify_from set
		self::set_enter_session('verify_from', 'username_change');

		// send code way
		self::send_code_way();
	}
}
?>