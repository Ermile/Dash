<?php
namespace content_api\v6\app;


class controller
{
	public static function routing()
	{
		if(\dash\url::subchild())
		{
			\content_api\v6::no(404);
		}

		if(!\dash\request::is('get'))
		{
			\content_api\v6::no(400);
		}

		$detail = self::detail();

		\content_api\v6::bye($detail);
	}



	private static function detail()
	{
		$detail            = [];

		self::detail_v6($detail);

		if(is_callable(["\\lib\\app\\application", "detail_v6"]))
		{
			$my_detail = \lib\app\application::detail_v6();
			if(is_array($my_detail))
			{
				$detail = array_merge($detail, $my_detail);
			}
		}

		return $detail;
	}



	private static function detail_v6(&$detail)
	{
		self::lang($detail);

		self::url($detail);

		self::site($detail);

		self::version($detail);

		self::menu($detail);

		self::transalate($detail);

		self::navigation($detail);

		self::intro($detail);

		self::theme_default($detail);

		self::theme_night($detail);


	}



	private static function lang(&$detail)
	{
		$detail['lang_list'] = \dash\language::all();
	}


	private static function url(&$detail)
	{
		$detail['url']['site']    = \dash\url::site();
		$detail['url']['kingdom'] = \dash\url::kingdom();
		$detail['url']['domain']  = \dash\url::domain();
		$detail['url']['root']    = \dash\url::root();
	}


	private static function site(&$detail)
	{
		$detail['site']['name']   = T_(\dash\option::config('site','title'));
		$detail['site']['desc']   = T_(\dash\option::config('site','desc'));
		$detail['site']['slogan'] = T_(\dash\option::config('site','slogan'));
		$detail['site']['logo']   = \dash\url::static(). '/images/logo.png';
	}


	private static function version(&$detail)
	{
		$detail['version']                 = [];
		$detail['version']['last']         = '19.4';
		$detail['version']['deprecated']   = '14.0.1';
		$detail['version']['title']        = T_("This version is deprecated");
		$detail['version']['desc']         = T_("To download new version of this app click blow link");
		$detail['version']['btn']          = [];
		$detail['version']['btn']['title'] = T_("Site");
		$detail['version']['btn']['url']   = \dash\url::kingdom(). '/app';
	}


	private static function transalate(&$detail)
	{
		$transalate               = [];
		$transalate['version']    = T_("Version");
		$transalate['changelang'] = T_("Change language");
		$transalate['close']      = T_("Close");
		$transalate['back']       = T_("Back");
		$transalate['go']         = T_("Go");
		$transalate['enter']      = T_("Enter");

		// set translate into detail json
		$detail['transalate'] = $transalate;
	}




	private static function menu(&$detail)
	{
		$menu = [];
		// type enum(defile, url, api, menu, tel, email)
		$menu[] =
		[
			'icon'  => null,
			'type'  => 'menu',
			'title' => T_("About"),
			'link'   => \dash\url::kingdom(). '/api/v6/about',
			'child' =>
			[
				[
					'icon'  => null,
					'type'  => 'api',
					'title' => T_("Privacy"),
					'link'  => \dash\url::kingdom(). '/api/v6/privacy'
				],
			],
		];

		$menu[] =
		[
			'icon'  => null,
			'type'  => 'api',
			'title' => T_("Contact"),
			'link'  => \dash\url::kingdom(). '/api/v6/contact',
			'child' => [],
		];

		$menu[] =
		[
			'icon'  => null,
			'type'  => 'api',
			'title' => T_("Vision"),
			'link'  => \dash\url::kingdom(). '/api/v6/vision',
			'child' => [],
		];

		$menu[] =
		[
			'icon'  => null,
			'type'  => 'api',
			'title' => T_("Mission"),
			'link'  => \dash\url::kingdom(). '/api/v6/mission',
			'child' => [],
		];

		$menu[] =
		[
			'icon'  => null,
			'type'  => 'url',
			'title' => T_("Website"),
			'link'  => \dash\url::kingdom(),
			'child' => [],
		];


		$detail['menu']   = $menu;

	}


	private static function navigation(&$detail)
	{
		$navigation = [];
		// type enum(defile, url, api, navigation, tel, email)
		$navigation[] =
		[
			'icon'  => null,
			'type'  => 'navigation',
			'title' => T_("About"),
			'link'   => \dash\url::kingdom(). '/api/v6/about',

		];


		$detail['navigation']   = $navigation;

	}




	private static function intro(&$detail)
	{
		$intro   = [];
		$intro[] =
		[
			'title'       => T_('Travel to Karbala'),
			'desc'        => T_('Executor of first pilgrimage to the Ahl al-Bayt | Karbala - Mashhad - Qom'),
			'bg_from'  => '#ffffff',
			'bg_to'  => '#ffffff',
			'title_color' => '#000000',
			'desc_color'  => '#000000',
			'image'       => 'https://khadije.com/files/1/92-fd6f59d2284353db98bdf32e2d6796c8.png',
			'btn' =>
			[
				[
					'title' => T_("Next"),
					'action' => 'next',
				],
			],
		];

		$intro[] =
		[
			'title'       => T_('Travel to Qom'),
			'desc'        => T_('Executor of first pilgrimage to the Ahl al-Bayt | Karbala - Mashhad - Qom'),
			'bg_from'  => '#ffffff',
			'bg_to'  => '#ffffff',
			'title_color' => '#000000',
			'desc_color'  => '#000000',
			'image'       => 'https://khadije.com/files/1/90-7de485580f96aefb3e1c70f445565028.png',
			'btn' =>
			[
				[
					'title' => T_("Prev"),
					'action' => 'prev',
				],
				[
					'title' => T_("Next"),
					'action' => 'next',
				],
			],
		];

		$intro[] =
		[
			'title'       => T_('Travel to Mashhad'),
			'desc'        => T_('Executor of first pilgrimage to the Ahl al-Bayt | Karbala - Mashhad - Qom'),
			'bg_from'  => '#ffffff',
			'bg_to'  => '#ffffff',
			'title_color' => '#000000',
			'desc_color'  => '#000000',
			'image'       => 'https://khadije.com/files/1/91-b688f1d8b2ba6f076558b8d97bbc615e.png',
			'btn' =>
			[
				[
					'title' => T_("Prev"),
					'action' => 'prev',
				],
				[
					'title' => T_("Next"),
					'action' => 'next',
				],
			],
		];

		$intro[] =
		[
			'title'       => T_('Khadije Charity'),
			'desc'        => T_('Executor of first pilgrimage to the Ahl al-Bayt | Karbala - Mashhad - Qom'),
			'bg_from'  => '#ffffff',
			'bg_to'  => '#ffffff',
			'title_color' => '#000000',
			'desc_color'  => '#000000',
			'image'       => 'https://khadije.com/files/1/431-22327c753b4d65d22873fc545e2dd7c1.png',
			'btn' =>
			[
				[
					'title' => T_("Start"),
					'action' => 'start',
				],
			],
		];


		$detail['intro'] = $intro;
	}


	private static function theme_default(&$detail)
	{
		$theme_default           = [];
		$theme_default['splash'] =
		[
			'bg_from' => '#eee',
			'bg_to'   => '#eee',
			'color'   => '#fff',
		];

		$theme_default['global'] =
		[
			'bg_from'   => '#eee',
			'bg_to'     => '#eee',
			'color'     => '#fff',
			'btn_from'  => '#eee',
			'btn_to'    => '#eee',
			'btn_color' => '#fff',
		];

		$theme_default['intro'] =
		[
			'bg_from'      => '#eee',
			'bg_to'        => '#eee',
			'color'        => '#fff',
			'header_from'  => '#eee',
			'header_to'    => '#eee',
			'header_color' => '#fff',
			'footer_from'  => '#eee',
			'footer_to'    => '#eee',
			'footer_color' => '#fff',
		];

		$theme_default['share'] =
		[
			'bg_from'      => '#eee',
			'bg_to'        => '#eee',
			'color'        => '#fff',
			'header_from'  => '#eee',
			'header_to'    => '#eee',
			'header_color' => '#fff',
			'footer_from'  => '#eee',
			'footer_to'    => '#eee',
			'footer_color' => '#fff',
		];

		$theme_default['btn'] =
		[
			"success" =>
			[
				'bg_from' => '#eee',
				'bg_to'   => '#eee',
				'color'   => '#fff',
			],
			"danger" =>
			[
				'bg_from' => '#eee',
				'bg_to'   => '#eee',
				'color'   => '#fff',
			],
			"warn" =>
			[
				'bg_from' => '#eee',
				'bg_to'   => '#eee',
				'color'   => '#fff',
			],
			"info" =>
			[
				'bg_from' => '#eee',
				'bg_to'   => '#eee',
				'color'   => '#fff',
			],
		];

		$detail['theme']['default'] = $theme_default;
	}

	private static function theme_night(&$detail)
	{

		$theme_default           = [];
		$theme_default['splash'] =
		[
			'bg_from' => '#eee',
			'bg_to'   => '#eee',
			'color'   => '#fff',
		];

		$theme_default['global'] =
		[
			'bg_from'   => '#eee',
			'bg_to'     => '#eee',
			'color'     => '#fff',
			'btn_from'  => '#eee',
			'btn_to'    => '#eee',
			'btn_color' => '#fff',
		];

		$theme_default['intro'] =
		[
			'bg_from'      => '#eee',
			'bg_to'        => '#eee',
			'color'        => '#fff',
			'header_from'  => '#eee',
			'header_to'    => '#eee',
			'header_color' => '#fff',
			'footer_from'  => '#eee',
			'footer_to'    => '#eee',
			'footer_color' => '#fff',
		];

		$theme_default['share'] =
		[
			'bg_from'      => '#eee',
			'bg_to'        => '#eee',
			'color'        => '#fff',
			'header_from'  => '#eee',
			'header_to'    => '#eee',
			'header_color' => '#fff',
			'footer_from'  => '#eee',
			'footer_to'    => '#eee',
			'footer_color' => '#fff',
		];

		$theme_default['btn'] =
		[
			"success" =>
			[
				'bg_from' => '#eee',
				'bg_to'   => '#eee',
				'color'   => '#fff',
			],
			"danger" =>
			[
				'bg_from' => '#eee',
				'bg_to'   => '#eee',
				'color'   => '#fff',
			],
			"warn" =>
			[
				'bg_from' => '#eee',
				'bg_to'   => '#eee',
				'color'   => '#fff',
			],
			"info" =>
			[
				'bg_from' => '#eee',
				'bg_to'   => '#eee',
				'color'   => '#fff',
			],
		];

		$detail['theme']['night'] = $theme_default;

	}
}
?>