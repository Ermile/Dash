<?php
namespace addons\content_enter;

class controller
{
	public static function routing()
	{

	}



	/**
	* check is set remeber me of this user
	*/
	public function check_remember_me()
	{
		if(\dash\db\sessions::get_cookie() && !\dash\user::login())
		{
			$user_id = \dash\db\sessions::get_user_id();

			if($user_id && is_numeric($user_id))
			{
				// set user id in static var
				self::$user_id = $user_id;
				// load user data by user id
				self::load_user_data('user_id');
				// set login session
				self::enter_set_login(null, true);
			}
		}
	}
}
?>