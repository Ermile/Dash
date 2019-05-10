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
			'name'             => 'Jibres',
			'short_name'       => 'Jibres',
			'display'          => 'standalone',
			'theme_color'      => '#ffffff',
			'background_color' => '#ffffff',
			'scope'            => '/',
			'start_url'        => \dash\url::kingdom(),
			'description'      => \dash\data::site_desc(),
			// 'orientation'      => '',
			'icons'            =>
			[
				[
					'src' => \dash\url::static().'/images/favicons/android-chrome-192x192.png',
					'sizes' => '192x192',
					'type' => 'image/png',
				],
				[
					'src' => \dash\url::static().'/images/favicons/android-chrome-512x512.png',
					'sizes' => '512x512',
					'type' => 'image/png',
				]
			]
		];

		\dash\code::jsonBoom($val);
	}
}
?>
