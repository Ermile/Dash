<?php
namespace content_api\home;


class controller
{
	public static function routing()
	{
		$module = \dash\url::module();

		if(!$module || ($module === 'doc' && !\dash\url::child()) || (in_array($module, ['v5', 'v6']) && !\dash\url::child()))
		{
			// nothing
		}
		else
		{
			\dash\header::status(404);
		}

		$result =
		[
			'website'            => \dash\url::kingdom(),
			'api-latest-version' => 6,
			'api-v5'             =>
			[
				'url'         => \dash\url::here(). '/v5',
				'expire-date' => '2019-02-20'
			],
			'api-v6' =>
			[
				'url' => \dash\url::here(). '/v6',
				'doc' => \dash\url::here(). '/v6/doc',
			],
			'lang' =>
			[
				'en' => \dash\url::site(). '/en/api',
				'fa' => \dash\url::site(). '/fa/api',
			],

		];

		\dash\notif::api($result);


	}
}
?>