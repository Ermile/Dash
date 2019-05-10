<?php
namespace dash\engine;
/**
 * Progressive Web Apps
 */
class pwa
{
	public static function manifest()
	{
		$siteTitle = \dash\option::config('site', 'title');
		$manifest  =
		[
			'name'             => T_($siteTitle). ' | '. T_(\dash\option::config('site', 'slogan')),
			'short_name'       => T_($siteTitle),
			'description'      => T_(\dash\option::config('site', 'desc')),
			'dir'              => \dash\language::current('direction'),
			'lang'             => str_replace('_', '-', \dash\language::current('iso')),


			'display'          => 'standalone',
			// phone top nav color
			// get color from settings
			'theme_color'      => '#ffffff',
			// background of splash
			// get color from settings
			'background_color' => '#1a2733',


			'scope'            => '/',
			'start_url'        => \dash\url::kingdom(). '?utm_source=pwa',
			'orientation'      => 'portrait',


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
			// ],
		];


		// set icons if exist
		$iconsArr = [];

		// check icon192
		$icon192 = '/images/logo/png/'. $siteTitle. '-logo-192.png';
		if(file_exists(root. 'public_html/static'. $icon192))
		{
			$icon192 = \dash\url::static(). $icon192;
			$iconsArr[] =
			[
				'type' => 'image/png',
				'sizes' => '192x192',
				'src' => $icon192,
			];
		}

		// check icon512
		$icon512 = '/images/logo/png/'. $siteTitle. '-logo-512.png';
		if(file_exists(root. 'public_html/static'. $icon512))
		{
			$icon512 = \dash\url::static(). $icon512;
			$iconsArr[] =
			[
				'type' => 'image/png',
				'sizes' => '512x512',
				'src' => $icon512,
			];
		}


		// check default logo
		$iconDefault = '/images/logo/'. $siteTitle. '.png';
		if(file_exists(root. 'public_html/static'. $iconDefault))
		{
			$iconDefault = \dash\url::static(). $iconDefault;
			$iconsArr[] =
			[
				'type' => 'image/png',
				'src' => $iconDefault,
			];
		}

		if($iconsArr)
		{
			$manifest['icons'] = $iconsArr;
		}



		\dash\code::jsonBoom($manifest, true, 'manifest');
	}


	public static function service_worker()
	{
		$worker = "
self.addEventListener('install', function() {
  console.log('Install!');
});

self.addEventListener('activate', event => {
  console.log('Activate!');
});

self.addEventListener('fetch', function(event) {
  console.log('Fetch!', event.request);
});


const FILES_TO_CACHE = [
  '/offline.html',
];

evt.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[ServiceWorker] Pre-caching offline page');
      return cache.addAll(FILES_TO_CACHE);
    })
);

evt.waitUntil(
    caches.keys().then((keyList) => {
      return Promise.all(keyList.map((key) => {
        if (key !== CACHE_NAME) {
          console.log('[ServiceWorker] Removing old cache', key);
          return caches.delete(key);
        }
      }));
    })
);

if (evt.request.mode !== 'navigate') {
  // Not a page navigation, bail.
  return;
}
evt.respondWith(
    fetch(evt.request)
        .catch(() => {
          return caches.open(CACHE_NAME)
              .then((cache) => {
                return cache.match('offline.html');
              });
        })
);

		";


		\dash\code::jsonBoom($worker, true, 'js');
	}

	public static function offline()
	{
		$off = "You are offline!";


		\dash\code::jsonBoom($off, true, 'js');
	}
}
?>
