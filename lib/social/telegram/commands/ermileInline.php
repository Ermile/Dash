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
			case 'iq_'. T_('about'):
			case 'iq_'. T_('detail'):
				self::iq_about();
				break;

			case 'iq_contact':
			case 'iq_address':
			case 'iq_tel':
			case 'iq_telephone':
			case 'iq_mobile':
			case 'iq_phone':
			case 'iq_email':
			case 'iq_'. T_('contact'):
			case 'iq_'. T_('address'):
			case 'iq_'. T_('tel'):
			case 'iq_'. T_('telephone'):
			case 'iq_'. T_('mobile'):
			case 'iq_'. T_('phone'):
			case 'iq_'. T_('email'):
				self::iq_contact();
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
					'thumb_url'             =>\dash\url::site().'/static/images/logo.png',
					'input_message_content' =>
					[
						'message_text' => $msg,
						'parse_mode'   => 'html'
					],
					'reply_markup' =>
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


	public static function iq_contact()
	{
		$msg = T_("Ermile, Floor2, Yas Building"). ', '. T_("1st alley, Haft-e-tir St"). ', '. T_("Qom"). ', '. T_("IRAN"). '.';

		$resultInline =
		[
			'results' =>
			[
				[
					'type'                  => 'venue',
					'id'                    => 1,
					'latitude'              => 34.6500896,
					'longitude'             => 50.8789642,
					'title'                 => T_(\dash\option::config('site', 'title')),
					'description'           => T_('Read more about us'),
					'address'               => $msg,
					'foursquare_id'         => '5bd1d8293b8307002bdb5dbb',
					'thumb_url'             =>\dash\url::site().'/static/images/logo.png',
					'reply_markup' =>
					[
						'inline_keyboard' =>
						[
							[
								[
									'text' => T_("Check website"),
									'url'  => \dash\url::kingdom(),
								],
							]
						]
					]
				]
			]
		];
		bot::answerInlineQuery($resultInline);
	}
}
?>