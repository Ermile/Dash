<?php
namespace dash\social\telegram;

class user extends tg
{

	public static function detect()
	{
		var_dump(hook::from());
		$myUser = \dash\app\user::get(['chatid' => hook::from(), 'limit' => 1]);
		var_dump($myUser);
		if(!$myUser)
		{
			$myUser = self::register();
		}
		if(!$myUser)
		{
			// user not detected
			var_dump('hello dolly');
			var_dump(\dash\notif::get());
			return null;
		}
		var_dump($myUser);




		exit();
	}

	public static function register()
	{
		$newUserDetail =
		[
			'firstname'    => null,
			'lastname'     => null,
			'displayname'  => null,
			'chatid'       => hook::from(),
			// 'mobile'       => null,
			// 'avatar'       => null,
			'status'       => 'active',
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