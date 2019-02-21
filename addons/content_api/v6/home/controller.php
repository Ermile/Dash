<?php
namespace content_api\v6\home;


class controller
{
	public static function routing()
	{
		$result =
		[
			'website' =>
			[
				'en' => \dash\url::site(). '/en',
				'fa' => \dash\url::site(). '/fa',
			],
			'url' =>
			[
				'en' => \dash\url::site(). '/en/api/v6',
				'fa' => \dash\url::site(). '/fa/api/v6',
			],
			'doc' =>
			[
				'en' => \dash\url::site(). '/en/api/v6/doc',
				'fa' => \dash\url::site(). '/fa/api/v6/doc',
			],
		];

		\dash\notif::api($result);
	}
}
?>