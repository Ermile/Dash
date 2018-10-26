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
				\dash\log::set('tg:user:block2Active');
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
			\dash\log::set('tg:user:notDetect');
			// user not detected
			return null;
		}

		// if user blocked us but send message via hook, change status to active
		if(isset($myUser['tgstatus']) && $myUser['tgstatus'] === 'block')
		{
			\dash\log::set('tg:user:block2Active2');
			self::active();
		}

		if(isset($myUser['id']))
		{
			\dash\app\tg\user::init($myUser['id']);
			return $myUser['id'];
		}

		\dash\log::set('tg:user:notDetect2');
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
			\dash\log::set('tg:user:register');
			return $result;
		}
		\dash\log::set('tg:user:register:fail');
		return false;
	}


	public static function block()
	{
		\dash\app\tg\user::status("block");
	}


	public static function active()
	{
		\dash\app\tg\user::status("active");
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

			tg::ok();
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
			tg::ok();
		}
		else
		{
			tg::sendMessage(['text' => T_('Registration failed!')]);
			tg::ok();
		}
	}


	public static function saveLanguage()
	{
		$newLang = null;
		switch (hook::cmd('command'))
		{
			// try to save en for user lang
			case '/english':
			case 'en_us':
				$newLang = 'en';
				break;

			// try to save fa for user lang
			case '/persian':
			case '/farsi':
			case 'fa_ir':
				$newLang = 'fa';
				break;

			case '/arabic':
			case 'ar_iq':
				$newLang = 'ar';
				break;

			default:
				break;
		}

		if($newLang)
		{
			\dash\app\tg\user::lang($newLang);
			// try to change laguage to selected
			\dash\language::set_language($newLang);
			// send success message
			if(isset(\dash\language::$data[$newLang]['localname']))
			{
				$newLang = \dash\language::$data[$newLang]['localname'];
			}
			$newLangMsg = T_('Your language was successfully set to :lang.', ['lang' => "<b>". T_($newLang)."</b>"] );
			tg::sendMessage(['text' => $newLangMsg]);

			tg::ok();
			return true;
		}
		if(\dash\app\tg\user::lang())
		{
			\dash\language::set_language(\dash\app\tg\user::lang());
		}
	}
}
?>