<?php
namespace dash\social\telegram;

class user extends tg
{

	public static function detect()
	{
		var_dump(hook::from());
		$myUser = \dash\app\user::get(['chatid' => hook::from(), 'limit' => 1]);
		if(!$myUser)
		{
			$myUser = self::register();
		}
		if(!$myUser)
		{
			// user not detected
			var_dump('hello dolly');
			var_dump($myUser);
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
			'chatid'       => null,
			// 'mobile'       => null,
			// 'avatar'       => null,
			'status'       => 'active',
		];
		$result = \dash\app\user::add($newUserDetail);
		if(isset($result['id']))
		{
			return $result['id'];
		}
		return false;
	}

}
?>