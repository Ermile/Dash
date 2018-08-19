<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class menu
{
	public static $return = false;

	public static function run($_cmd)
	{
		$response = null;
		switch ($_cmd['command'])
		{
			case 'main':
			case '/main':
			case 'mainmenu':
			case 'menu':
			case '/menu':
			case 'منو۰':
				$response = self::main();
				break;

			case 'return':
			case 'بازگشت':
				switch ($_cmd['text'])
				{
					case 'بازگشت به منوی اصلی':
					default:
						$response = user::start();
						break;
					case 'بازگشت به ثبت سفارش':
						$response = self::order();
						break;
				}
				// $response = self::returnBtn();
				break;

			default:
				break;
		}

		// automatically add return to end of keyboard
		if(self::$return)
		{
			// if has keyboard
			if(isset($response['reply_markup']['keyboard']))
			{
				$response['reply_markup']['keyboard'][] = ['بازگشت'];
			}
		}

		return $response;
	}


	/**
	 * create mainmenu
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function main($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				[T_("About")],
				[T_("Feedback"), T_("Contact")],
			],
		];

		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = T_("Main menu");

		$result =
		[
			// 'method'       => 'editMessageReplyMarkup',
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];

		// return menu
		return $result;
	}
}
?>