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
		if(\lib\db\sessions::get_cookie() && !\lib\user::login())
		{
			$user_id = \lib\db\sessions::get_user_id();

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