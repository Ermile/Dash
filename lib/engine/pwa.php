<?php
namespace dash\engine;
/**
 * Progressive Web Apps
 */
class pwa
{
	public static function manifest()
	{
		$val =
		[
			'name'             => T_(\dash\option::config('site', 'title')). ' | '. T_(\dash\option::config('site', 'slogan')),
			'short_name'       => T_(\dash\option::config('site', 'title')),
			'description'      => T_(\dash\option::config('site', 'desc')),
			'dir'              => \dash\language::current('direction'),
			'lang'             => str_replace('_', '-', \dash\language::current('iso')),


			'display'          => 'standalone',
			// phone top nav color
			'theme_color'      => '#ffffff',
			// background of splash
			'background_color' => '#1a2733',

			'scope'            => '/',
			'start_url'        => \dash\url::kingdom(). '?utm_source=pwa',
			'orientation'      => 'portrait',

			'icons'            =>
			[
				[
					'type' => 'image/png',
					'sizes' => '192x192',
					'src' => \dash\url::static().'/images/favicons/android-chrome-192x192.png',
				],
				[
					'type' => 'image/png',
					'sizes' => '512x512',
					'src' => \dash\url::static().'/images/favicons/android-chrome-512x512.png',
				]
			],
			// 'related_applications' =>
			// [
			// 	[
			// 		'platform' => 'play',
			// 		'url'      => 'https://play.google.com/store/apps/details?id=com.ermile.jibres',
			// 		'id'       => 'com.ermile.jibres'
			// 	],
			// 	[
			// 		'platform' => 'itunes',
			// 		'url'      => 'https://itunes.apple.com/app/ermile-jibres/id123456789',
			// 	]
			// ]
		];


		\dash\code::jsonBoom($val, true, 'manifest');
	}
}
?>
