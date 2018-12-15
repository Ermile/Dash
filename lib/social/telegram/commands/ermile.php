<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class ermile
{
	/**
	 * execute user request and return best result
	 * @param  [type] $_cmd [description]
	 * @return [type]       [description]
	 */
	public static function run($_cmd)
	{
		$siteTitle = \dash\option::config('site', 'title');
		$domanName = \dash\url::root();

		switch ($_cmd['command'])
		{
			case '/start':
			case 'start':
			case T_('start'):
				self::start($_cmd);
				break;

			case '/lang':
			case 'lang':
			case '/language':
			case T_('language'):
				self::lang();
				break;


			case '/menu':
			case T_('menu'):
			case '/mainmenu':
			case T_('mainmenu'):
			case '/return':
			case T_('return'):
				self::mainmenu();
				break;

			case '/about':
			case 'about':
			case T_('about'):
			case T_('detail'):
				self::about();
				break;

			case '/me':
			case 'me':
			case '/whoami':
			case T_('me'):
			case T_('whoami'):
				\dash\social\telegram\user::preview();
				break;

			case '/contact':
			case 'contact':
			case T_('contact'):
			case T_('address'):
			case T_('tel'):
			case T_('telephone'):
			case T_('mobile'):
			case T_('phone'):
			case T_('email'):
				self::contact();
				break;

			case '/website':
			case 'website':
			case T_('website'):
			// all type of site title
			case $siteTitle:
			case T_($siteTitle):
			case strtolower($siteTitle):
			case ucfirst($siteTitle):
			case '/'. $siteTitle:
			case '/'. T_($siteTitle):
			case '/'. strtolower($siteTitle):
			// all type of domain name
			case $domanName:
			case T_($domanName):
			case strtolower($domanName):
			case ucfirst($domanName):
			case '/'. $domanName:
			case '/'. T_($domanName):
			case '/'. strtolower($domanName):
				self::website();
				break;

			case '/register':
			case '/signup':
			case '/sync':
			case 'register':
			case 'signup':
			case 'sync':
			case T_('register'):
			case T_('signup'):
			case T_('sync'):
			case T_('connect'):
			case 'cb_register':
			case 'cb_sync':
				self::register();
				break;

			case 'type_contact':
			case 'type_location':
			case 'type_audio':
			case 'type_document':
			case 'type_photo':
			case 'type_sticker':
			case 'type_video':
			case 'type_voice':
			case 'type_venue':
				self::getExtraType($_cmd);
				break;

			case '/help':
			case 'help':
			case '/ls':
			case '/?':
			case '/؟':
			case T_('help'):
			case 'ls':
			case '؟':
			case '?':
				self::help();
				break;


			default:
				break;
		}
	}


	/**
	 * start conversation
	 * @return [type] [description]
	 */
	public static function start($_cmd)
	{
		if(isset($_cmd['optional']))
		{
			$extraCmd = substr($_cmd['text'], 7);
			// check find --lang= and if exist set lang for this user
			$langExist0 = strpos($extraCmd, '--lang=');
			if($langExist0 !== false)
			{
				$langCmd   = substr($extraCmd, $langExist0, 9);
				$langParam = substr($langCmd, 7, 9);
				if(strlen($langParam) === 2)
				{
					// remove lang command from code
					$extraCmd = str_replace($langCmd, '', $extraCmd);
					$extraCmd = trim($extraCmd);
					// we have new lang
					// set it for user and ui
					\dash\social\telegram\user::setLanguage($langParam);
				}
			}

			if($extraCmd)
			{
				$newCmd = \dash\social\telegram\hook::cmd(null, $extraCmd);

				\dash\social\telegram\answer::finder($newCmd);
				return;
			}
		}

		$result = [];

		$result['text'] = T_('Hello!'). "\n";
		$result['text'] .= T_('We are so glad to meet you.'). "\n\n";

		$result['text'] .= "<a href='". bot::website(). "'>".T_(\dash\option::config('site', 'title')). "</a>". "\n";
		$result['text'] .= T_(\dash\option::config('site', 'slogan')). "\n\n";
		// $result['text'] .= bot::website(). "\n";
		$result['text'] .= '/help'. "\n";
		$result['text'] .= T_('Made by @Ermile');

		$result['disable_web_page_preview'] = false;

		bot::sendMessage($result);
		bot::ok();
	}


	/**
	 * show about message
	 * @return [type] [description]
	 */
	public static function about()
	{
		$msg = "<b>".T_(\dash\option::config('site', 'title')). "</b>\n";
		$msg .= T_(\dash\option::config('site', 'slogan')). "\n\n";
		$msg .= T_(\dash\option::config('site', 'desc'));

		$result = [];
		$result['method']  = "sendPhoto";
		$result['photo']   = \dash\url::static(). '/images/logo.png';
		if(\dash\url::isLocal())
		{
			$result['photo']   = \dash\url::protocol(). '://'. \dash\url::root() .'.com/static/images/logo.png';
		}
		$result['caption'] = $msg;
		$result['reply_markup'] =
		[
			'inline_keyboard' =>
			[
				[
					[
						'text' => T_("Check website"),
						'url'  => bot::website(),
					],
				],
				[
					[
						'text' => T_("Read more about us"),
						'url'  => bot::website(). '/about',
					],
				]
			]
		];

		bot::sendPhoto($result);
		bot::ok();
	}


	/**
	 * show contact message
	 * @return [type] [description]
	 */
	public static function contact()
	{
		// get location address from http://www.gps-coordinates.net/
		$address = T_("Ermile, Floor2, Yas Building"). ', '. T_("1st alley, Haft-e-tir St"). ', '. T_("Qom"). ', '. T_("IRAN"). '.';
		$result =
		[
			'method'    => "sendVenue",
			'latitude'  => '34.6500896',
			'longitude' => '50.8789642',
			'title'     => T_(\dash\option::config('site', 'title')),
			'address'   => $address,
			'foursquare_id' => '5bd1d8293b8307002bdb5dbb',
			'text'      => T_("We are happy to see you!"),
			'reply_markup' =>
			[
				'inline_keyboard' =>
				[
					[
						[
							'text' => T_("Check website"),
							'url'  => bot::website(),
						],
					],
					[
						[
							'text'          => T_("Submit new ticket"),
							'callback_data' => 'ticket',
						],
					]
				]
			]
		];

		if(!bot::isPrivate())
		{
			$result['reply_markup']['inline_keyboard'][1] =
			[
				[
					'text' => T_("Send feedback"),
					'url'  => bot::deepLink('ticket'),
				],
			];
		}

		bot::sendVenue($result);
		bot::ok();
	}


	public static function website()
	{
		$msg = "<a href='". bot::website(). "'>".T_(\dash\option::config('site', 'title')). "</a>". "\n";
		$msg .= T_(\dash\option::config('site', 'slogan')). "\n\n";
		$msg .= T_(\dash\option::config('site', 'desc')). "\n";
		$msg .= bot::website();

		$result = [];
		$result['text'] = $msg;
		$result['disable_web_page_preview'] = false;

		$result['reply_markup'] =
		[
			'inline_keyboard' =>
			[
				[
					[
						'text' => T_("Open :val website", ['val' => T_(\dash\option::config('site', 'title'))]),
						'url'  => bot::website(),
					],
				],
				[
					[
						'text' => T_(":val Telegram bot", ['val' => T_(\dash\option::config('site', 'title'))]),
						'url'  => bot::deepLink()
					],
				]
			]
		];

		bot::sendMessage($result);
		bot::ok();
	}


	/**
	 * show help message
	 * @return [type] [description]
	 */
	public static function help()
	{
		$text = T_("You can control me by sending these commands"). "\r\n\n";
		$text .= "/start ". T_("Start again"). "\n";
		$text .= "/lang ". T_('Change your language'). "\n";
		$text .= "/menu ". T_('Go to main menu'). "\n";
		$text .= "/register ". T_('Sync with website'). "\n";
		$text .= "/about ". T_('About'). "\n";
		$text .= "/contact ". T_("Contact us"). "\n";
		$text .= "/ticket ". T_("Add new ticket"). "\n";
		$text .= "/help ". T_("Get help commands"). "\n";
		// $text .= "/contact contact us\n";
		$result =
		[
			'text' => $text,
			'reply_markup' =>
			[
				'inline_keyboard' =>
				[
					[
						[
							'text' => T_("Website"),
							'url'  => bot::website(),
						],
					]
				]
			]
		];
		bot::sendMessage($result);
		bot::ok();
	}


	/**
	 * get phone number from user contact
	 * @return [type] [description]
	 */
	public static function getExtraType($_cmd = null)
	{
		// output text
		$text = '';

		// if is fake return false;
		switch ($_cmd['command'])
		{
			case 'type_contact':
				if($_cmd['argument'] === 'fake')
				{
					if($_cmd['optional'])
					{
						$text = T_("We do not need contact of anothers!"). ' '. T_('You can share your contact by press on /register'). ' 😕';
					}
					else
					{
						$text = T_("We need mobile number to complete registeration process."). ' 🙃';
					}
				}
				break;

			case 'type_audio':
					$text = T_("I don't have enough time for listening!"). ' '. T_('Type for me:)');
				break;

			case 'type_sticker':
					$text = T_("Thanks for your kindness"). ' 😃';
				break;

			case 'type_video':
					$text = T_("I'm busy and i dont think have time to watch video!"). ' 😎';
				break;

			case 'type_voice':
					$text = T_("A long time until I want to recognize your voice!!"). ' 😭';
				break;

			default:
					$text = T_("I'm still not advanced enough!"). ' 😉';
				break;
		}
		$result =
		[
			'text' => $text,
			'reply_to_message_id' => true,
		];

		bot::sendMessage($result);
		bot::ok();
	}


	public static function register()
	{

		// if start with callback answer callback
		if(bot::isCallback())
		{
			bot::answerCallbackQuery(T_("Sync account"));
		}

		$result['text'] .= "\n". T_('You can connect complete your registeration on :val from telegram by share your mobile number.', ['val' => T_(\dash\option::config('site', 'title'))]);
		$result['text'] .= "\n\n". T_('By press share contact we get your mobile and after that you can use our website and your account is synced.');
		$result['text'] .= "\n\n". T_('Also you can do it anytime you need with /register command.');

		// add replymarkup keyboard
		$result['reply_markup'] =
		[
			'keyboard' =>
			[
				[ ["text" => T_("Register with mobile"), "request_contact" => true] ],
				[T_("Help"), T_("Return to main menu")],
			],
		];
		bot::sendMessage($result);
		bot::ok();
	}



	public static function lang($_withStart = null)
	{
		$msg = '';
		if($_withStart)
		{
			$msg .= T_('Hello!'). "\n";
			$msg .= T_('We are so glad to meet you.'). "\n\n";
		}
		// generate messaage
		switch (\dash\language::current())
		{
			case 'fa':
				$msg .= '🇮🇷 '. 'لطفا زبان خود را انتخاب کنید'. "\n";
				$msg .= '🇬🇪 '. "Please choose your language". "\n";
				break;

			case 'en':
				$msg .= '🇬🇪 '. "Please choose your language". "\n";
				$msg .= '🇮🇷 '. 'لطفا زبان خود را انتخاب کنید'. "\n";
				break;

			default:
				$msg .= T_("Please choose your language"). "\n";
				break;
		}
		// add extra line
		$msg .= "\n";

		$keyboard = [];
		$langList = \dash\language::all();
		foreach ($langList as $key => $value)
		{
			if($key === 'fa')
			{
				$msg .= "/persian 🇮🇷". "\n";
				$keyboard[] = [ $value['iso']. " ". $value['localname']." 🇮🇷"];
			}
			elseif($key === 'en')
			{
				$msg .= "/english 🇬🇪". "\n";
				$keyboard[] = [ $value['iso']. " ". $value['localname']." 🇬🇪"];
			}
			elseif($key === 'ar')
			{
				$msg .= "/arabic". "\n";
				$keyboard[] = [ $value['iso']. " ". $value['localname'] ];
			}
			else
			{
				$msg .= "/". $value['name']. "\n";
				$keyboard[] = ["/". $value['iso']];
			}
		}
		// create result
		$result = ['text' => $msg];

		$result['reply_markup'] =
		[
			'keyboard' => $keyboard
		];
		// send message
		bot::sendMessage($result);
		bot::ok();
	}


	public static function mainmenu($_onlyMenu = false)
	{
		if(is_callable('\lib\tg\detect::mainmenu'))
		{
			return \lib\tg\detect::mainmenu($_onlyMenu);
		}

		// remove keyboard on groups
		$menu = ['remove_keyboard' => true];
		// on private chat add keyboard
		if(bot::isPrivate())
		{
			$menu =
			[
				'keyboard' => [],
				'resize_keyboard' => true,
			];

			// add about and contact link
			$menu['keyboard'][] = [T_("About"), T_("Contact")];

			// add sync
			if(\dash\user::detail('mobile'))
			{
				$menu['keyboard'][] = [T_("Website"). ' '. T_(\dash\option::config('site', 'title'))];
			}
			else
			{
				$menu['keyboard'][] = [T_("Sync with website")];
			}
		}
		// define keyboard in private message

		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = T_("Main menu");

		$result =
		[
			'text'                => $txt_text,
			'reply_markup'        => $menu,
		];

		bot::sendMessage($result);
		bot::ok();
	}
}
?>