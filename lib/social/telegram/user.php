<?php
namespace dash\social\telegram;

class user
{
	public static function detect()
	{
		if(\dash\user::id())
		{
			$userStatus = \dash\user::detail('tgstatus');
			// if user blocked, change status to unblock
			if($userStatus === 'block')
			{
				\dash\log::set('tg:user:block2Active');
				self::active();
			}
			elseif($userStatus === 'callback')
			{
				\dash\log::set('tg:user:callback2Active');
				self::active();
			}
			elseif($userStatus === 'inline')
			{
				\dash\log::set('tg:user:inline2Active');
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
		if(!hook::from())
		{
			\dash\log::set('tg:user:idNotFound');
			return false;
		}
		$newUserDetail =
		[
			'firstname'   => hook::from('first_name'),
			'lastname'    => hook::from('last_name'),
			'title'       => hook::from('username'),
			'chatid'      => hook::from(),
			'tgusername'  => hook::from('username'),
			// 'mobile'   => null,
			// 'avatar'   => null,
			'status'      => 'active',
			'tgstatus'    => 'active',
		];

		if(tg::isCallback())
		{
			$newUserDetail['tgstatus'] = 'callback';
		}
		if(tg::isInline())
		{
			$newUserDetail['tgstatus'] = 'inline';
		}

		$result = \dash\app\tg\user::add($newUserDetail);

		if($result)
		{
			\dash\log::set('tg:user:register:ok');
			// show message of add new user
			// clean notif messages
			\dash\notif::clean();
		}
		else if($result == false)
		{
			\dash\log::set('tg:user:register:fail');
		}
		else if($result == null)
		{
			\dash\log::set('tg:user:register:exist');
		}
		else
		{
			\dash\log::set('tg:user:register:unknown');
		}

		return $result;
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
		if(isset($from['first_name']) && isset($contact['first_name']) && $from['first_name'] !== $contact['first_name'])
		{
			tg::sendMessage(['text' => T_('Why your name is different!')]);
		}
		if(isset($from['last_name']) && isset($contact['last_name']) && $from['last_name'] !== $contact['last_name'])
		{
			tg::sendMessage(['text' => T_('Why your family is different!')]);
		}

		// finally try to save chat id for this user
		$registerResult = \dash\app\tg\account::register($contact['user_id'], $mobile, $from);
		// say okay
		tg::ok();
		// if user send contact detail
		$result = [];
		$result['reply_markup'] =
		[
			'inline_keyboard' =>
			[
				[
					[
						'text' => T_("Enter in :val website", ['val' => T_(\dash\option::config('site', 'title'))]),
						'url'  => \dash\url::kingdom(). '/enter?autosend=true&mobile='. $mobile,
					]
				]
			]
		];

		if($registerResult)
		{
			$result['text'] = T_('Your phone number registered successfully'). ' '. T_('Thank you.'). ' 😉';
		}
		else if($registerResult === null)
		{
			// user exist before this share contact
			$result['text'] = T_('We have your mobile before this!'). ' '. T_('Thank you.'). ' 😉';
		}
		else
		{
			$result['text'] = T_('Registration failed!');
		}

		// send message on each conditions
		tg::sendMessage($result);
	}


	public static function saveLanguage()
	{
		$newLang = null;
		$inputMsg = hook::cmd('command');
		if($inputMsg === '/start')
		{
			if(hook::from('language_code') === 'en-US')
			{
				// on english does not give from tg
				// because many of user use en design
			}
			else
			{
				// get from user language_code
				$inputMsg = mb_strtolower(hook::from('language_code'));
			}
		}
		switch ($inputMsg)
		{
			// try to save en for user lang
			case '/english':
			case 'en_us':
			case 'en-us':
				$newLang = 'en';
				break;

			// try to save fa for user lang
			case '/persian':
			case '/farsi':
			case 'fa_ir':
			case 'fa-ir':
				$newLang = 'fa';
				break;

			case '/arabic':
			case 'ar_iq':
			case 'ar-iq':
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
			$newLangMsg = T_('Your language was successfully set to :lang.', ['lang' => "<b>". T_($newLang)."</b>"] ). ' /language';
			tg::sendMessage(['text' => $newLangMsg]);

			tg::ok();
			return true;
		}

		if(\dash\app\tg\user::lang())
		{
			\dash\language::set_language(\dash\app\tg\user::lang());
		}
		else
		{
			// try to get language from user
			\dash\social\telegram\commands\ermile::lang(true);
		}
	}


	public static function setAvatar($_userid, $_file)
	{
		// check if user does not have avatar avatar

		return null;
	}


	public static function preview($_userid = null, $_args = null, $_msg = null)
	{
		tg::ok();
		$myDetail = '';
		if(!$_userid)
		{
			$_userid = hook::from();

			// create detail of caption
			$myDetail = "<code>". hook::from(). "</code>\n";
			$myDetail .= hook::from('first_name');
			$myDetail .= ' '. hook::from('last_name'). "\n";
			$myDetail .= "@". hook::from('username'). "\n";
			$myDetail .= "#profile <code>" . \dash\user::id(). "</code>";

			$userLastPhoto = file::lastProfilePhoto($_userid);

			if($userLastPhoto)
			{
				$photoResult =
				[
					'photo'   => $userLastPhoto,
					'caption' => $myDetail,
					'reply_markup' =>
					[
						'inline_keyboard' =>
						[
							[
								[
									'text' => T_("More detail"),
									'callback_data'  => 'userid',
								],
							]
						]
					]
				];
				tg::sendPhoto($photoResult);
			}
			else
			{
				tg::sendMessage(['text' => $myDetail]);
			}
		}
		else
		{
			// create detail of caption
			$myDetail = "<code>". $_userid. "</code>\n";
			if($_args['first_name'])
			{
				$myDetail .= $_args['first_name'];
			}
			if($_args['last_name'])
			{
				$myDetail .= ' '. $_args['last_name'];
			}
			$myDetail .= "\n";
			if($_args['username'])
			{
				$myDetail .= "@". $_args['username']. "\n";
			}
			$myDetail .= "#profile";
			if($_msg)
			{
				$myDetail .= "\n". $_msg;
			}

			$userLastPhoto = file::lastProfilePhoto($_userid);

			if($userLastPhoto)
			{
				$photoResult =
				[
					'photo'   => $userLastPhoto,
					'caption' => $myDetail,
				];
				tg::sendPhoto($photoResult);
			}
			else
			{
				tg::sendMessage(['text' => $myDetail]);
			}
		}

	}
}
?>