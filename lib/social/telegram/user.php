<?php
namespace dash\social\telegram;

class user
{

	public static function detect()
	{
		if(\dash\user::id())
		{
			// if user blocked, change status to unblock
			if(\dash\user::detail('tgstatus') === 'block')
			{
				self::active();
			}
			return \dash\user::id();
		}

		$myUser = \dash\app\tg\user::get(hook::from());
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
			\dash\app\tg\user::init($myUser['id']);
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
		$result = \dash\app\tg\user::add($newUserDetail);
		if($result)
		{
			return $result;
		}
		return false;
	}

	public static function registerOnTheFly($_tgResponse)
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

				$result = \dash\app\tg\user::add($newUserDetail);
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
		$a = \dash\app\tg\user::status("block");
	}


	public static function active()
	{
		$a = \dash\app\tg\user::status("active");
	}


	public static function saveContact()
	{
		$contact = hook::contact(null);
		// if user is not sended contact return null
		if(!$contact)
		{
			return null;
		}

		$from    = hook::from(null);
		$mobile  = null;
		// if mobile isset, use it
		if(isset($contact['phone_number']))
		{
			$mobile = $contact['phone_number'];
		}
		else
		{
			// we dont have mobile number for this contact!
			tg::$hook['message']['contact']['fake'] = true;
			tg::$hook['message']['contact']['phone_number'] = false;
			tg::sendMessage(['text' => T_('We need mobile number!')]);
			return false;
		}

		// check id is the same
		if($from['id'] !== $contact['user_id'])
		{
			// set fake value for this contact
			tg::$hook['message']['contact']['fake'] = true;
			tg::sendMessage(['text' => T_('We dont need another users contact:?)')]);
			return false;
		}
		if($from['first_name'] !== $contact['first_name'])
		{
			tg::sendMessage(['text' => T_('Why your name is different!')]);
		}
		if($from['last_name'] !== $contact['last_name'])
		{
			tg::sendMessage(['text' => T_('Why your family is different!')]);
		}

		// finally try to save chat id for this user
		$registerResult = \dash\app\tg\account::register($contact['user_id'], $mobile, $from);
		if($registerResult)
		{
			// if user send contact detail then save all of his/her profile photos
			tg::sendMessage(['text' => T_('Your phone number registered successfully;)')]);
		}
		else
		{
			tg::sendMessage(['text' => T_('Registration failed!')]);
		}
	}
}
?>