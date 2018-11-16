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

		// if start with callback answer callback
		if(bot::isCallback())
		{
			$callbackResult =
			[
				'text' => T_("Ask from support")
			];
			bot::answerCallbackQuery($callbackResult);
		}

		return self::step1();
	}


	public static function step1()
	{
		// after this go to next step
		step::plus();

		$txt_text = T_("Thank you for choosing us.")."\n\n";
		$txt_text .= T_("Knowing your valuable comments about bugs and problems and more importantly your precious offers will help us in this way.")."\n\n";
		$txt_text .= T_("Please wrote about your problem and describe it.");

		$result   =
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


	public static function step2($_desc)
	{
		if(bot::isCallback())
		{
			$callbackResult =
			[
				'text' => T_("Please wrote about your ticket")." 📝",
				'show_alert' => true,
			];
			bot::answerCallbackQuery($callbackResult);
			return false;
		}
		elseif(step::checkFalseTry())
		{
			return false;
		}

		// set desc of ticket
		step::set('ticketDesc', $_desc);
		// get confirm of create
		step::plus();

		$txt_text = '🆕 '. T_("This is your new ticket")."\n\n";
		$txt_text .= "<b>". $_desc . "</b>"."\n\n";
		$txt_text .= T_("Do you confirm?");

		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' =>
			[
				'keyboard' => [[T_('Yes'), T_('Cancel')]],
				'resize_keyboard' => true,
				'one_time_keyboard' => true
			],
		];
		bot::sendMessage($result);
	}


	public static function step3($_accept)
	{
		if(bot::isCallback())
		{
			$callbackResult =
			[
				'text' => T_("Please use below menu")." 📝",
				'show_alert' => true,
			];
			bot::answerCallbackQuery($callbackResult);
			return false;
		}
		elseif(step::checkFalseTry())
		{
			return false;
		}

		if($_accept === T_('Yes') || $_accept === 'y' || $_accept === 'ok')
		{
			\dash\app\tg\ticket::create(step::get('ticketDesc'));
		}
		else
		{
			bot::sendMessage(T_("Cancel operation."));
		}
		step::stop();
	}
}
?>