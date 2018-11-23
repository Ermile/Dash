<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class hafez
{
	public static function run($_cmd)
	{
		switch ($_cmd['command'])
		{
			case '/fal':
			case 'fal':
			case '/hafez':
			case 'hafez':
			case 'فال':
			case 'فالگیر':
			case 'فال بگیر':
			case 'فال حافظ':
			case 'حافظ':
			case 'تفعل':
			case 'خواجه':
			case 'بگو ای خواجه':
			case 'خواجه شیرازی':
			case 'ای حافظ شیرازی':
				self::niyat();
				return true;
				break;


			case '/fal1':
			case 'cb_hafez_say_something':
				// if start with callback answer callback

				if(bot::isCallback())
				{
					self::fal();
				}
				else
				{

					$falCode = \dash\utility\convert::to_en_number($_cmd['optional']);
					if($falCode && is_numeric($falCode))
					{
						self::fal($falCode);
					}

				}
				break;


			case 'cb_hafez_read_it':
				// if start with callback answer callback
				if(bot::isCallback())
				{
					self::falReader($_cmd);
				}
				break;

		}

		return false;
	}


	private static function niyat()
	{
		bot::ok();

		$text .= '#افل_حافظ'. "\n";
		$text .= 'میگن اول نیت کنید بعد فال بگیرید'. "\n";
		$text .= 'هر وقت آماده بودید می‌تونید دکمه زیر رو بزنید تا فالتون رو تحویل بگیرید'. "\n\n";
		$text .= 'ای حافظ شیرازی'. "\n";
		$text .= 'تو محرم هر رازی'. "\n";
		$text .= ' تو را به خدا و به شاخ نبات ات قسم می دهم که هر چه صلاح و مصلحت می بینی برایم آشکار و آرزوی مرا بر آورده سازی.'. "\n";
		$text .= ''. "\n";

		$result =
		[
			'text' => $text,
			'reply_markup' =>
			[
				'inline_keyboard' =>
				[
					[
						[
							'text' => 'بریم حافظ جون',
							'callback_data' => 'hafez_say_something',
						],
					]
				]
			]
		];
		bot::sendMessage($result);
	}


	private static function fal()
	{
		bot::ok();
		bot::answerCallbackQuery(T_("Fal of Hafez"));

		$myFal  = \dash\utility\hafez::tg();
		// add fal code
		$text   = '🎲 '. T_('Fal of Hafez');
		$myCode = null;
		if(isset($myFal['code']))
		{
			$text   .= ' #'. $myFal['code']. "\n\n";
			$myCode = $myFal['code'];
		}
		// add poem
		if(isset($myFal['poemPretty']))
		{
			$text .= $myFal['poemPretty']. "\n\n";
		}
		// add mean
		if(isset($myFal['meanPretty']))
		{
			$text .= "<b>". $myFal['meanPretty']. "</b>";
		}

		$result =
		[
			'text' => $text,
			'reply_markup' =>
			[
				'inline_keyboard' =>
				[
					[
						[
							'text' => T_("Read it for me"),
							'callback_data' => 'hafez_read_it '. $myCode,
						],
					]
				]
			]
		];
		bot::sendMessage($result);
	}



	private static function falReader($_cmd)
	{
		bot::ok();
		bot::answerCallbackQuery(T_("Fal of Hafez"). ' 🎻');

		$myCode = null;
		if(isset($_cmd['optional']))
		{
			$myCode = $_cmd['optional'];
		}

		// get random fal from hafez
		$myFalAddr = \dash\utility\hafez::file($myCode);
		$text      .= '<b>'. T_('Fal of Hafez'). "</b>". ' #'. $myCode. "\n";
		$text      .= T_('Page'). ' '. $myCode. "\n";
		// $text   .= ''. "\n";
		$result =
		[
			'caption'      => $text,
			'audio'        => $myFalAddr,
			'reply_markup' =>
			[
				'inline_keyboard' =>
				[
					[
						[
							'text' => T_("Open :val website", ['val' => T_(\dash\option::config('site', 'title'))]),
							'url'  => bot::website(),
						],
					]
				]
			]
		];

		bot::sendAudio($result);
	}

}
?>