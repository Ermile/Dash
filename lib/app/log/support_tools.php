<?php
namespace dash\app\log;

class support_tools
{
	private static $load = [];

	public static function load($_args)
	{
		if(empty(self::$load))
		{
			if(isset($_args['code']))
			{
				self::$load = \dash\db\comments::get(['id' => $_args['code'], 'limit' => 1]);
			}
		}

		return self::$load;
	}


	public static function tg_btn($_code)
	{
		return
		[
			'inline_keyboard'    =>
			[
				[
					[
						'text' => 	T_("Visit in site"),
						'url'  => \dash\url::base(). '/!'. $_code,
					],
				],
				[
					[
						'text'          => 	T_("Check ticket"),
						'callback_data' => 'ticket '. $_code,
					],
				],
				[
					[
						'text'          => 	T_("Answer"),
						'callback_data' => 'ticket '. $_code. ' answer',
					],
				],
			],
		];
	}

	public static function tg_btn2($_code)
	{
		return
		[
			'inline_keyboard'    =>
			[
				[
					[
						'text' => 	T_("Visit in site"),
						'url'  => \dash\url::base(). '/!'. $_code,
					],
				],
				[
					[
						'text'          => 	T_("Check ticket"),
						'callback_data' => 'ticket '. $_code,
					],
				],
			],
		];
	}


	public static function plus($_args)
	{
		$plus = isset($_args['data']['plus']) ? $_args['data']['plus'] : null;
		if($plus)
		{
			\dash\utility\human::fitNumber($plus);
		}

		return $plus;
	}


	public static function code($_args)
	{
		$code = isset($_args['code']) ? $_args['code'] : null;
		return $code;
	}


	public static function via($_args)
	{
		$via = isset($_args['via']) ? $_args['via'] : null;
		return $via;
	}
}
?>