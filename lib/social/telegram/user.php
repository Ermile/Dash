<?php
namespace dash\social\telegram;

class user
{

	public static function detect()
	{
		if(\dash\user::id())
		{
			return \dash\user::id();
		}

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

		// if user blocked us but send message via hook, change status to active
		if(isset($myUser['tgstatus']) && $myUser['tgstatus'] === 'block')
		{
			self::active();
		}

		if(isset($myUser['id']))
		{
			\dash\user::init_tg($myUser['id']);
			return $myUser['id'];
		}

		return false;
	}


	private static function register()
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
			'tgstatus'    => 'active',
		];
		$result = \dash\app\user::add_f($newUserDetail);
		if($result)
		{
			return $result;
		}
		return false;
	}


	public static function block()
	{
		\dash\app\user::edit(['tgstatus' => 'block'], \dash\user::id());
	}


	public static function active()
	{
		\dash\app\user::edit(['tgstatus' => 'active'], \dash\user::id());
	}
}
?>