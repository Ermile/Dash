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
		$response = null;
		switch ($_cmd['command'])
		{
			case '/start':
			case 'start':
			case 'شروع':
				$response = self::start();
				break;

			case '/about':
			case 'about':
			case 'درباره':
			case 'درباره ی':
			case 'درباره‌ی':
				$response = self::about();
				break;

			case '/me':
			case 'me':
			case '/whoami':
			case 'whoami':
			case 'من کیم':
			case 'من کیم؟':
			case 'بگیر':
			case 'پروفایل':
			case 'من':
				$response = self::me();
				break;

			case '/contact':
			case 'contact':
			case 'تماس':
			case 'آدرس':
			case 'ادرس':
			case 'نشانی':
				$response = self::contact();
				break;

			case '/register':
			case 'register':
			case '/signup':
			case 'signup':
				$response = self::signup();

				break;

			case 'type_contact':
				$response = self::register('شماره موبایل', $_cmd);
				break;

			case 'type_location':
				$response = self::register('آدرس');
				break;

			case 'type_audio':
			case 'type_document':
			case 'type_photo':
			case 'type_sticker':
			case 'type_video':
			case 'type_voice':
			case 'type_venue':
				$response = self::register($_cmd['command'], $_cmd);
				break;

			case '/help':
			case 'help':
			case '/ls':
			case 'ls':
			case '؟':
			case '?':
			case 'کمک':
			case 'راهنمایی':
			case '/?':
			case '/؟':
				$response = self::help();
				break;

			case '/feedback':
			case 'feedback':
			case 'ثبت بازخورد':
				\dash\social\telegram\step::set('menu', menu::main(true));
				$response = step_feedback::start();
				break;

			default:
				break;
		}
		return $response;
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

		return $result;
	}


	/**
	 * show about message
	 * @return [type] [description]
	 */
	public static function about()
	{
		$result = [];
		$result['method']  = "sendPhoto";
		$result['photo']   = \dash\url::site().'/static/images/logo.png';
		$result['caption'] = T_("Ermile is inteligent");

		bot::sendPhoto($result);

		return $result;
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
			'title'     => T_("Ermile"),
			'address'   => $address,
			'text'      => T_("We are happy to see you!"),
		];

		bot::sendVenue($result);

		return $result;
	}


	/**
	 * show help message
	 * @return [type] [description]
	 */
	public static function help()
	{
		$text = \dash\url::domain()."\r\n\n";
		$text .= "You can control me by sending these commands:\r\n\n";
		$text .= "/start start conversation\n";
		$text .= "/about about\n";
		$text .= "/contact contact us\n";
		$text .= "/menu show main menu\n";
		// $text .= "/contact contact us\n";
		$result =
		[
			'text'         => $text,
		];
		bot::sendMessage($result);

		return $result;
	}


	/**
	 * get phone number from user contact
	 * @return [type] [description]
	 */
	public static function register($_type = null, $_cmd = null)
	{
		if(!$_type)
		{
			return false;
		}
		// output text
		$text = $_type. ' شما با موفقیت ثبت شد.';
		// if is fake return false;
		switch ($_cmd['command'])
		{
			case 'type_contact':
				if($_cmd['argument'] === 'fake')
				{
					if($_cmd['optional'])
					{
						$text = 'ما به اطلاعات مخاطب شما نیاز داریم، نه سایر کاربران!';
					}
					else
					{
						$text = 'ما برای ثبت‌نام به شماره موبایل احتیاج داریم!';
					}
				}
				break;

			case 'type_audio':
					$text = 'من فرصت آهنگ گوش کردن ندارم!';
				break;

			case 'type_sticker':
					$text = 'ممنون از ابراز لطف شما';
				break;

			case 'type_video':
					$text = 'حسابی سرم شلوفه، فکر نکنم وقت فیلم دیدن باشه!';
				break;

			case 'type_voice':
					$text = 'خیلی مونده تا بخوام صدا رو تشخیص بدم!';
				break;

			default:
					$text = 'من هنوز اونقدر پیشرفته نشدم!';
				break;
		}
		$result =
		[
			'text'  => $text,
		];

		bot::sendMessage($result);

		return $result;
	}


	/**
	 * show user details!
	 * @return [type] [description]
	 */
	public static function me()
	{
		$result =
		[
			'method'      => 'getUserProfilePhotos',
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

		return $result;
	}
}
?>