<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;
use \dash\social\telegram\step;

class step_ticketAnswer
{
	public static function start($_cmd)
	{
		// its okay on start
		bot::ok();

		step::set('ticketNo', \dash\utility\convert::to_en_number($_cmd['optional']));
		step::start('ticketAnswer');

		// if start with callback answer callback
		if(bot::isCallback())
		{
			$result =
			[
				'text' => T_("Answer to ticket "). $_cmd['optional'],
			];
			bot::answerCallbackQuery($result);
		}

		return self::step1();
	}


	public static function step1()
	{
		step::plus();
		$txt_text = T_("Please wrote your answer");

		// empty keyboard
		$result =
		[
			'text'         => $txt_text,
			'reply_markup' =>
			[
				'keyboard' => [[T_('Cancel')]],
				'resize_keyboard' => true,
				'one_time_keyboard' => true

			],
		];
		bot::sendMessage($result);
	}


	public static function step2($_answer)
	{
		if(bot::isCallback())
		{
			$result =
			[
				'text' => T_("Please wrote your answer")." 📝",
				'show_alert' => true,
			];
			bot::answerCallbackQuery($result);
			return false;
		}

		$ticketNo = step::get('ticketNo');
		\dash\app\tg\ticket::answer($ticketNo, $_answer);

		step::stop();
	}
}
?>