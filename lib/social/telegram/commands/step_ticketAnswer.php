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
				'keyboard' => [['/cancel']],
				'resize_keyboard' => true,
				'one_time_keyboard' => true

			],
		];
		bot::sendMessage($result);
	}


	public static function step2($_answer)
	{
		$ticketNo = step::get('ticketNo');
		\dash\app\tg\ticket::answer($ticketNo, $_answer);

		step::stop();
	}
}
?>