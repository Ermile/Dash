<?php
namespace dash\social\telegram;

class user extends tg
{

	public static function detect()
	{
		$myUser = \dash\app\user::get(['chatid' => hook::from(), 'limit' => 1]);
		// var_dump(hook::from());
		// var_dump($myUser);
		// if not exist try to register
		if(!isset($myUser['id']))
		{
			$myUser = self::register();
		}
		// if not exist yet return null
		if(!$myUser)
		{
			// user not detected
			// var_dump(\dash\notif::get());
			return null;
		}

		if(isset($myUser['id']))
		{
			\dash\user::init_code($myUser['id']);
			return $myUser['id'];
		}

		return false;
	}

	public static function register()
	{
		$newUserDetail =
		[
			'firstname'   => hook::from('first_name'),
			'lastname'    => hook::from('last_name'),
			'title'       => hook::from('username'),
			'chatid'      => hook::from(),
			// 'mobile'   => null,
			// 'avatar'   => null,
			'status'      => 'active',
		];
		$result = \dash\app\user::add_f($newUserDetail);
		if(isset($result['id']))
		{
			return $result['id'];
		}
		return false;
	}

}
?>