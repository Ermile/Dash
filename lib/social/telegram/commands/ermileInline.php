<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class ermileInline
{
	public static function run($_cmd)
	{
		if(bot::isInline())
		{
			bot::ok();
		}

		switch ($_cmd['command'])
		{
			case 'iq_about':
				self::iq_about();
				break;

			default:
				break;
		}
	}


	public static function iq_about()
	{
		$msg = "<b>".T_(\dash\option::config('site', 'title')). "</b>\n";
		$msg .= T_(\dash\option::config('site', 'slogan')). "\n\n";
		$msg .= T_(\dash\option::config('site', 'desc'));

		$resultInline =
		[
			'results' =>
			[
				[
					'type'                  => 'article',
					'id'                    => 1,
					'title'                 => T_('About'),
					'description'           => T_('Read more about us'),
					'thumb_url'             =>\dash\url::site().'/static/images/logo.jpg',
					'input_message_content' =>
					[
						'message_text' => $msg,
						'parse_mode'   => 'html'
					],
					'reply_markup'          =>
					[
						'inline_keyboard' =>
						[
							[
								[
									'text' => T_("Check website"),
									'url'  => \dash\url::kingdom(),
								],
							],
							[
								[
									'text' => T_("Read more about us"),
									'url'  => \dash\url::kingdom(). '/about',
								],
							]
						]
					],
				]
			]
		];
		bot::answerInlineQuery($resultInline);
	}
}
?>