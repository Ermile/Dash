<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;
use \dash\social\telegram\step;

class step_ticketCreate
{
	public static function start()
	{
		// its okay on start
		bot::ok();

		step::start('ticketCreate');

		return self::step1();
	}


	public static function step1()
	{
		// after this go to next step
		step::plus();

		$txt_text = T_("Thank you for choosing us.")."\n\n";
		$txt_text .= T_("Knowing your valuable comments about bugs and problems and more importantly your precious offers will help us in this way.")."\n\n";
		$txt_text .= T_("Please enter your ticket title.");

		$result   =
		[
			'text'         => $txt_text,
		];

		bot::sendMessage($result);
	}


	public static function step2($_ticketDetail)
	{
		\dash\app\tg\ticket::create($_ticketDetail);

		step::stop();
	}
}
?>