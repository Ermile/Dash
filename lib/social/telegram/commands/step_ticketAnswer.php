<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;
use \dash\social\telegram\step;

class step_ticketAnswer
{
	public static function start($_cmd)
	{
		// if we have ticket number continue else return
		if(isset($_cmd['optional']) && $_cmd['optional'])
		{
			step::set('ticketNo', \dash\utility\convert::to_en_number($_cmd['optional']));
			step::start('ticketAnswer');

			return self::step1($_cmd);
		}
		else
		{
			return self::requireCode();
		}
	}


	public static function step1()
	{
		$ticketNo = step::get('ticketNo');
		$txt_text = \dash\app\tg\ticket::list($ticketNo);
		$keyboard = [];
		bot::ok();

		if($txt_text)
		{
			// after this go to next step
			step::plus();
			$keyboard[] = [ T_("Answer"), T_("Cancel") ];

			$result   =
			[
				'text'         => $txt_text,
				'reply_markup' =>
				[
					'keyboard' => $keyboard,
					'resize_keyboard' => true,
					'one_time_keyboard' => true
				]
			];

			bot::sendMessage($result);
		}
		else
		{
			$txt_text = T_("We cant find detail of this ticket!");
			bot::sendMessage($txt_text);
			step::stop();
		}
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
			step::checkFalseTry();
			return;
		}

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
		];
		bot::sendMessage($result);
		bot::ok();
	}

}
?>