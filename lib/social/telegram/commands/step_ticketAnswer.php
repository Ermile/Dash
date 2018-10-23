<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;
use \dash\social\telegram\step;

class step_ticketAnswer
{
	private static $menu = ["remove_keyboard" => true];

	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_cmd)
	{
		// if we have ticket number continue else return
		if(isset($_cmd['optional']) && $_cmd['optional'])
		{
			step::set('ticketNo', $_cmd['optional']);
			step::start('ticketAnswer');

			return self::step1($_cmd);
		}
		else
		{
			return self::requireCode();
		}
	}


	/**
	 * show thanks message
	 * @return [type] [description]
	 */
	public static function step1()
	{
		// after this go to next step
		step::plus();

		// show give contact menu
		$menu     = self::$menu;
		$ticketNo = step::get('ticketNo');
		$txt_text = \dash\app\tg\ticket::list($ticketNo);
		// $txt_text = T_("What do you want to do?")."\n\n";
		$keyboard = [];
		$keyboard[] = [ T_("Answer"), T_("Cancel") ];


		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' =>
			[
				'keyboard' => $keyboard,
				'one_time_keyboard' => true
			]
		];

		bot::sendMessage($result);
		bot::ok();
	}


	public static function step2($_btn)
	{
		// after this go to next step

		if($_btn === T_("Answer"))
		{
			step::plus();
			$txt_text = T_("Please wrote your answer");
		}
		else
		{
			$txt_text = T_("Please choose from defined answer.");
		}

		$menu     = self::$menu;
		$result =
		[
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];
		bot::sendMessage($result);
		bot::ok();
	}


	public static function step3($_answer)
	{
		$ticketNo = step::get('ticketNo');
		\dash\app\tg\ticket::answer($ticketNo, $_answer);

		if(!$_answer)
		{
			return false;
		}

		bot::ok();

		step::stop();
	}


	public static function requireCode()
	{
		$result =
		[
			'text'         => T_("We need ticket number!")." 🙁",
			'reply_markup' => self::$menu,
		];
		bot::sendMessage($result);
		bot::ok();
	}

}
?>