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
			if(isset($_cmd['argument']))
			{
				return self::step1($_cmd);
			}
		}
		else
		{
			return self::requireCode();
		}
	}


	public static function step1($_btn)
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
		bot::ok();
	}


	public static function step2($_answer)
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


	public static function show($_cmd)
	{
		$ticketNo = \dash\utility\convert::to_en_number($_cmd['optional']);
		$txt_text = \dash\app\tg\ticket::list($ticketNo);
		bot::ok();

		if($txt_text)
		{
			$result =
			[
				'text'         => $txt_text,
				'reply_markup' =>
				[
					'inline_keyboard' =>
					[
						[
							[
								'text' => T_("Visit in site"),
								'url'  => \dash\url::base(). '/!'. $ticketNo,
							],
						],
						[
							[
								'text'          => 	T_("Answer"),
								'callback_data' => 'ticket '. $ticketNo. ' answer',
							],
						],
					]
				]
			];

			bot::sendMessage($result);
		}
		else
		{
			$txt_text = T_("We can't find detail of this ticket!");
			bot::sendMessage($txt_text);
		}
	}
}
?>