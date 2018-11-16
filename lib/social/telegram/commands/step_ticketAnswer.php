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
			$callbackResult =
			[
				'text' => T_("Answer to ticket "). $_cmd['optional'],
			];
			bot::answerCallbackQuery($callbackResult);
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
			$callbackResult =
			[
				'text' => T_("Please wrote your answer")." 📝",
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
		step::set('ticketTxtAnswer', $_answer);
		// get confirm of create
		step::plus();

		$txt_text = '📢 '. T_("This is your answer")."\n\n";
		$txt_text .= "<b>". $_answer . "</b>"."\n\n";
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
			\dash\app\tg\ticket::answer(step::get('ticketNo'), step::get('ticketTxtAnswer'));
		}
		else
		{
			bot::sendMessage(T_("Cancel operation."));
		}
		step::stop();
	}
}
?>