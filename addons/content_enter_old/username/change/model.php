<?php
namespace content_enter\username\change;


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
		if(\dash\request::post('type') === 'remove')
		{
			// set session verify_from username remove
			self::set_enter_session('verify_from', 'username_remove');

			// send code way
			self::send_code_way();

			return;
		}

		if(!\dash\request::post('usernameNew'))
		{
			\dash\notif::error(T_("Plese fill the new username"));
			return false;
		}

		if(mb_strlen(\dash\request::post('usernameNew')) < 5)
		{
			\dash\notif::error(T_("You must set large than 5 character in new username"));
			return false;
		}

		if(mb_strlen(\dash\request::post('usernameNew')) > 50)
		{
			\dash\notif::error(T_("You must set less than 50 character in new username"));
			return false;
		}


		if(\dash\user::login('username') == \dash\request::post('usernameNew'))
		{
			\dash\notif::error(T_("Please select a different username"));
			return false;
		}


		// check username exist
		$check_exist_name = \dash\db\users::get_by_username(\dash\request::post('usernameNew'));

		if(!empty($check_exist_name))
		{
			\dash\notif::error(T_("This username alreay taked!"));
			return false;
		}


		if(\dash\request::post('usernameNew'))
		{
			self::set_enter_session('temp_username', \dash\request::post('usernameNew'));
		}

		// set session verify_from set
		self::set_enter_session('verify_from', 'username_change');

		// send code way
		self::send_code_way();
	}
}
?>