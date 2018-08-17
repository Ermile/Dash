<?php
namespace dash\social\telegram;

class user extends tg
{

	public static function detect()
	{
		$myUser = \dash\app\user::get(['chatid' => hook::from(), 'limit' => 1]);
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
			\dash\user::init_tg($myUser['id']);
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
		if($result)
		{
			return $result;
		}
		return false;
	}

}
?>