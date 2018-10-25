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
		switch ($_cmd['command'])
		{
			case '/start':
			case T_('start'):
				self::start();
				break;

			case '/lang':
			case '/language':
			case T_('language'):

				self::lang();
				break;

			case '/about':
			case T_('about'):
			case T_('detail'):
				self::about();
				break;

			case '/me':
			case '/whoami':
			case T_('me'):
			case T_('whoami'):
				self::me();
				break;

			case '/contact':
			case T_('contact'):
			case T_('address'):
			case T_('tel'):
			case T_('telephone'):
			case T_('mobile'):
			case T_('phone'):
			case T_('website'):
			case T_('email'):
				self::contact();
				break;

			case '/register':
			case '/signup':
			case T_('register'):
			case T_('signup'):
				self::signup();

				break;

			case 'type_contact':

				self::register($_cmd);
				break;

			case 'type_location':
				self::register($_cmd);
				break;

			case 'type_audio':
			case 'type_document':
			case 'type_photo':
			case 'type_sticker':
			case 'type_video':
			case 'type_voice':
			case 'type_venue':
				self::register($_cmd);
				break;

			case '/help':
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
	public static function start()
	{
		$result =
		[
			// 'reply_markup' => menu::main(true),
		];

		$result['text'] = T_('Haloo');
		$result['text'] .= "\n". T_('We are so glad to meet you.');
		$result['text'] .= "\n\n".  '/help';
		$result['text'] .= "\n". T_('Made by @Ermile');

		bot::sendMessage($result);
		bot::ok();
	}


	/**
	 * show about message
	 * @return [type] [description]
	 */
	public static function about()
	{
		$result = [];
		$result['method']  = "sendPhoto";
		// $result['photo']   = \dash\url::site().'/static/images/logo.png';
		$result['photo']   = 'https://ermile.com/static/images/logo.png';
		$result['caption'] = T_(\dash\option::config('site', 'desc'));
		$result['reply_markup'] =
		[
			'inline_keyboard' =>
			[
				[
					[
						'text' => T_("Check website"),
						'url'  => \dash\url::kingdom(),
					],
				],
				[
					[
						'text' => T_("Read more about us"),
						'url'  => \dash\url::kingdom(). '/about',
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
							'url'  => \dash\url::kingdom(),
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

		bot::sendVenue($result);
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
		$text .= "/about ". T_('About'). "\n";
		$text .= "/contact ". T_("Contact us"). "\n";
		$text .= "/ticket ". T_("Add new ticket"). "\n";
		$text .= "/help ". T_("or ?"). ' '. T_("Get help commands"). "\n";
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
							'url'  => \dash\url::kingdom(),
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
	public static function register($_cmd = null)
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
						$text = T_("We do not need contact of anothers!"). ' '. T_('You can share your contact by press on /register');
					}
					else
					{
						$text = T_("We need mobile number to complete registeration process.");
					}
				}
				break;

			case 'type_audio':
					$text = T_("I don't have enough time for listening!"). ' '. T_('Type for me:)');
				break;

			case 'type_sticker':
					$text = T_("Thanks for your kindness");
				break;

			case 'type_video':
					$text = T_("I'm busy and i dont think have time to watch video!");
				break;

			case 'type_voice':
					$text = T_("A long time until I want to recognize your voice!!");
				break;

			default:
					$text = T_("I'm still not advanced enough!");
				break;
		}
		$result =
		[
			'text' => $text,
		];

		bot::sendMessage($result);
		bot::ok();
	}


	/**
	 * show user details!
	 * @return [type] [description]
	 */
	public static function me()
	{
		$result =
		[
			'method' => 'getUserProfilePhotos',
		];

		return $result;
	}


	public static function signup()
	{
		$result['text'] = T_('Haloo');
		$result['text'] .= "\n". T_('You can connect your telegram with your mobile number in our service.');
		$result['text'] .= "\n\n". T_('Also you can do it anytime you need with /register command.');

		// add replymarkup keyboard
		$result['reply_markup'] =
		[
			'keyboard' =>
			[
				[ ["text" => T_("Register with mobile"), "request_contact" => true] ],
				[T_("Help"), T_("Cancel")],
			],
			'one_time_keyboard' => true
		];
		bot::sendMessage($result);
		bot::ok();
	}



	public static function lang()
	{
		// generate messaage
		$msg      = T_("Please choose your language"). "\n\n";
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
			'keyboard' => $keyboard,
			'one_time_keyboard' => true
		];
		// send message
		bot::sendMessage($result);
		bot::ok();
	}
}
?>