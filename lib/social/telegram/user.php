<?php
namespace dash\social\telegram;

class user
{
	public static function detect()
	{
		if(\dash\user::id())
		{
			// user exist and user_telegram not exist
			if(!\dash\app\tg\user::get(hook::from()))
			{
				\dash\app\tg\user::set(hook::from());
			}

			$userStatus = \dash\app\tg\user::detail('status');
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
			elseif($userStatus === null)
			{
				self::active();
			}
			// set username if not exist
			if(\dash\app\tg\user::detail('username') === null)
			{
				// set username
				self::setTgUserName();
			}
			// change username if changed
			if(\dash\app\tg\user::detail('username') !== hook::from('username'))
			{
				\dash\log::set('tg:username:changed');
				self::setTgUserName();
			}

			// update last active time
			self::setTgLastUpdate();

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
		if(isset($myUser['status']) && $myUser['status'] === 'block')
		{
			\dash\log::set('tg:user:block2Active2');
			self::active();
		}

		if(isset($myUser['user_id']))
		{
			\dash\app\tg\user::init($myUser['user_id']);
			return $myUser['user_id'];
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
			'firstname' => hook::from('first_name'),
			'lastname'  => hook::from('last_name'),
			'title'     => hook::from('username'),
			'chatid'    => hook::from(),
			'username'  => hook::from('username'),
			// 'mobile'   => null,
			// 'avatar'   => null,
			'status'      => 'active',
		];

		if(tg::isCallback())
		{
			$newUserDetail['status'] = 'callback';
		}
		if(tg::isInline())
		{
			$newUserDetail['status'] = 'inline';
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


	public static function setTgUserName()
	{
		\dash\app\tg\user::username(hook::from('username'));
	}


	public static function setTgLastUpdate()
	{
		\dash\app\tg\user::tgUpdateActivityTime(true);
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
						'url'  => tg::website(). '/enter?autosend=true&mobile='. $mobile,
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
		commands\ermile::mainmenu();
	}


	public static function saveLanguage()
	{
		$inputMsg   = hook::cmd('command');
		$newLang    = null;
		$autoDetect = null;

		switch ($inputMsg)
		{
			// try to save en for user lang
			case '/english':
			case '/en':
			case 'en_us':
			case 'en-us':
				$newLang = 'en';
				break;

			// try to save fa for user lang
			case '/persian':
			case '/farsi':
			case '/fa':
			case 'fa_ir':
			case 'fa-ir':
				$newLang = 'fa';
				break;

			case '/arabic':
			case '/ar':
			case 'ar_iq':
			case 'ar-iq':
				$newLang = 'ar';
				break;
		}

		if($newLang)
		{
			self::setLanguage($newLang);

			// send success message
			$newLangTitle = $newLang;
			if(isset(\dash\language::$data[$newLang]['localname']))
			{
				$newLangTitle = \dash\language::$data[$newLang]['localname'];
			}
			$newLangMsg = T_('Your language was successfully set to :lang.', ['lang' => "<b>". T_($newLangTitle)."</b>"] ). ' /language';
			tg::sendMessage(['text' => $newLangMsg]);
			tg::ok();

			return true;
		}

		// get user current lang
		$userSavedLang = \dash\app\tg\user::lang();
		if($userSavedLang)
		{
			self::setLanguage($userSavedLang, false);
			return true;
		}
		else
		{
			$langCode = hook::from('language_code');
			if($langCode === 'fa' || $langCode === 'fa-IR')
			{
				self::setLanguage('fa');
				return true;
			}
		}

		// if user in step and send something except command skip
		if(step::alive())
		{
			return null;
		}
		if(tg::isInline() || tg::isChosenInline() || tg::isCallback())
		{
			return null;
		}
		if($inputMsg === '/start' && hook::cmd('optional'))
		{
			// if started with speceial char
			return null;
		}
		if(!tg::isPrivate())
		{
			// dont check language on public chats
			return null;
		}

		// try to get language from user
		commands\ermile::lang(true);
	}


	public static function setLanguage($_newLang, $_saveUserDetail = true)
	{
		if($_saveUserDetail)
		{
			// save for user
			\dash\app\tg\user::lang($_newLang);
		}
		// try to change laguage to selected
		\dash\language::set_language($_newLang);
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