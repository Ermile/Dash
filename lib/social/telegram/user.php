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

	private static function registerOnTheFly($_tgResponse)
	{
		if(isset($_tgResponse['ok']) && $_tgResponse['ok'] === true)
		{
			if(isset($_tgResponse['result']['from']['id']))
			{
				$tgFrom = $_tgResponse['result']['from'];

				$newUserDetail =
				[
					// 'firstname' => '',
					// 'lastname'  => '',
					// 'title'     => '',
					// 'chatid'    => '',
					// 'avatar'    => null,
					'status'      => 'active',
					'tgstatus'    => 'active',
				];
				// fill some detail if exist
				if(isset($tgFrom['id']))
				{
					$newUserDetail['chatid'] = $tgFrom['id'];
				}
				if(isset($tgFrom['firstname']))
				{
					$newUserDetail['firstname'] = $tgFrom['firstname'];
				}
				if(isset($tgFrom['lastname']))
				{
					$newUserDetail['lastname'] = $tgFrom['lastname'];
				}
				if(isset($tgFrom['username']))
				{
					$newUserDetail['title'] = $tgFrom['username'];
				}

				$result = \dash\app\user::add_f($newUserDetail);
				if($result)
				{
					return $result;
				}
			}
		}

		return false;
	}

	public static function block()
	{
		$a = \dash\app\user::edit(['tgstatus' => 'block'], \dash\user::id());
		var_dump($a);
		var_dump(\dash\user::id());
		var_dump(\dash\notif::get());
	}


	public static function active()
	{
		\dash\app\user::edit(['tgstatus' => 'active'], \dash\user::id());
	}
}
?>