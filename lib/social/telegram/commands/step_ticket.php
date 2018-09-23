<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;
use \dash\social\telegram\step;
use \dash\social\telegram\hook;

class step_ticket
{
	private static $menu = ["remove_keyboard" => true];

	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start()
	{
		step::start('ticket');

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
		$txt_text = T_("We can give your ticket easily in via telegram")."\n\n";
		$txt_text .= T_("Please enter your ticket title");

		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];

		bot::sendMessage($result);

		// return menu
		return $result;
	}


	public static function step2($_feedback)
	{
		// after this go to next step
		step::plus();

		$txt_text = T_("Ticket title is saved.")."\n\n";
		$txt_text .= T_("Please wrote your problem");

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
		// after this go to next step

		$txt_text = T_("Your ticket is successfully saved.")."\n\n";
		$txt_text .= T_("We try to answer to you as soon as posible.");

		$result =
		[
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];
		bot::sendMessage($result);

		step::stop();
		return $result;
	}
}
?>