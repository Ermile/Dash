<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;
use \dash\social\telegram\step;
use \dash\social\telegram\hook;

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
		$txt_text = T_("What do you want to do?")."\n\n";
		$keyboard = [];
		$keyboard[] = [ T_("Answer"), T_("View") ];


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

		// return menu
		return $result;
	}


	public static function step2($_feedback)
	{
		// after this go to next step
		step::plus();
		$txt_text = 'Ticket Step2';
		$menu     = self::$menu;

		if($_feedback === '/Answer')
		{
			$txt_text = T_("Please wrote your answer");
		}

		$result =
		[
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];
		bot::sendMessage($result);

		return $result;
	}


	public static function step3($_feedback)
	{
		if(!$_feedback)
		{
			return false;
		}

		$ticketNo = step::get('ticketNo');
		\dash\app\tg\ticket::answer($ticketNo, $_feedback);

		// after this go to next step
		$menu     = self::$menu;
		$txt_text = T_("We are save you answer to this ticket.")."\n\n";
		$txt_text .= T_("Thanks.");

		$result =
		[
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];
		bot::sendMessage($result);

		step::stop();
		return $result;
	}


	public static function requireCode()
	{
		$result =
		[
			'text'         => T_("We need ticket number!")." 🙁",
			'reply_markup' => self::$menu,
		];
		bot::sendMessage($result);

		return $result;
	}

}
?>