<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;
use \dash\social\telegram\step;
use \dash\social\telegram\hook;

class step_ticketCreate
{
	private static $menu = ["remove_keyboard" => true];

	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start()
	{
		step::start('ticketCreate');

		return self::step1();
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

		$txt_text = T_("Thank you for choosing us.")."\n\n";
		$txt_text .= T_("Knowing your valuable comments about bugs and problems and more importantly your precious offers will help us in this way.")."\n\n";
		$txt_text .= T_("Please enter your ticket title.");

		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];

		bot::sendMessage($result);
		bot::ok();
	}


	public static function step2($_ticketDetail)
	{
		// after this go to next step

		$txt_text = T_("Your ticket is successfully saved.")."\n\n";
		$txt_text .= T_("We try to answer to you as soon as posible.");

		// show give contact menu
		$menu     = self::$menu;
		\dash\app\tg\ticket::create($_ticketDetail);

		$result =
		[
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];
		bot::sendMessage($result);
		bot::ok();

		step::stop();
	}
}
?>