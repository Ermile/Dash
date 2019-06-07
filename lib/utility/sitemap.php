<?php
namespace dash\utility;


class sitemap
{
	private static $show_result      = [];
	private static $current_language = null;
	private static $default_language = null;


	public static function create()
	{
		// set log to create new sitemap
		\dash\log::set('sitemapGenerate');

		// make new show_result
		self::$show_result = [];

		self::$current_language = \dash\language::current();
		self::$default_language = \dash\language::primary();


		// create sitemap for each language
		$site_url = \dash\url::site().'/';
		$sitemap  = new \dash\utility\sitemap_generator($site_url , root.'public_html/', 'sitemap' );

		self::static_page($sitemap);

		self::posts($sitemap);

		self::pages($sitemap);

		self::help_center($sitemap);

		self::attachments($sitemap);

		self::cats($sitemap);

		self::tags($sitemap);

		self::other($sitemap);

		self::current_project($sitemap);

		$sitemap->createSitemapIndex();

		return self::$show_result;

	}

	// can call from current project to show result
	// \lib\sitemap::create();
	public static function show_result($_key, $_count)
	{
		self::$show_result[$_key] = $_count;
	}


	private static function static_page(&$sitemap)
	{
		$static_page =
		[
			'about'     =>  ['0.6', 'weekly'],
			'pricing'   =>  ['0.6', 'weekly'],
			'terms'     =>  ['0.4', 'weekly'],
			'privacy'   =>  ['0.4', 'weekly'],
			'changelog' =>  ['0.5', 'daily'],
			'contact'   =>  ['0.6', 'weekly'],
			'logo'      =>  ['0.8', 'monthly'],
		];

		// add list of static pages
		$sitemap->addItem('', '1', 'daily');

		foreach ($static_page as $key => $value)
		{
			$sitemap->addItem($key, $value[0], $value[1]);
		}

		// PERSIAN
		// add all language static page automatically
		// we must detect pages automatically and list static pages here
		$lang_data        = \dash\language::all();

		if(is_array($lang_data))
		{
			foreach ($lang_data as $myLang => $value)
			{
				if($myLang != self::$current_language)
				{
					foreach ($static_page as $key => $value)
					{
						$sitemap->addItem($myLang. '/'. $key, $value[0], $value[1]);
					}
				}
			}
		}

	}


	private static function posts(&$sitemap)
	{
		// add posts
		$post = \dash\db\sitemap::posts();

		if(!is_array($post))
		{
			return;
		}

		self::show_result(__FUNCTION__, count($post));

		foreach ($post as $row)
		{
			$myUrl = $row['url'];
			if($row['language'] && $row['language'] !== self::$default_language)
			{
				$myUrl = $row['language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.8', 'daily', $row['publishdate']);
		}
	}


	private static function pages(&$sitemap)
	{
		// add pages
		$page = \dash\db\sitemap::pages();

		if(!is_array($page))
		{
			return;
		}

		self::show_result(__FUNCTION__, count($page));

		foreach ($page as $row)
		{
			$myUrl = $row['url'];
			if($row['language'] && $row['language'] !== self::$default_language)
			{
				$myUrl = $row['language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.6', 'weekly', $row['publishdate']);
		}
	}



	private static function help_center(&$sitemap)
	{
		// add helps
		$help = \dash\db\sitemap::help_center();

		if(!is_array($help))
		{
			return;
		}

		self::show_result(__FUNCTION__, count($help));

		foreach ($help as $row)
		{
			$myUrl = $row['url'];
			if($row['language'] && $row['language'] !== self::$default_language)
			{
				$myUrl = $row['language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.3', 'monthly', $row['publishdate']);
		}
	}


	private static function attachments(&$sitemap)
	{
		// add attachments
		$attachments = \dash\db\sitemap::attachments();

		if(!is_array($attachments))
		{
			return;
		}

		self::show_result(__FUNCTION__, count($attachments));

		foreach ($attachments as $row)
		{
			$myUrl = $row['url'];
			if($row['language'] && $row['language'] !== self::$default_language)
			{
				$myUrl = $row['language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.2', 'weekly', $row['publishdate']);
		}
	}


	private static function cats(&$sitemap)
	{
		// add cats
		$cats = \dash\db\sitemap::cats();

		if(!is_array($cats))
		{
			return;
		}

		self::show_result(__FUNCTION__, count($cats));

		foreach ($cats as $row)
		{
			$myUrl = $row['url'];
			if($row['language'] && $row['language'] !== self::$default_language)
			{
				$myUrl = $row['language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.5', 'weekly', $row['datecreated']);
		}
	}


	private static function tags(&$sitemap)
	{
		// add tags
		$tags = \dash\db\sitemap::tags();

		if(!is_array($tags))
		{
			return;
		}

		self::show_result(__FUNCTION__, count($tags));

		foreach ($tags as $row)
		{
			$myUrl = $row['url'];
			if($row['language'] && $row['language'] !== self::$default_language)
			{
				$myUrl = $row['language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.5', 'weekly', $row['datecreated']);
		}
	}


	private static function other(&$sitemap)
	{
		// add other
		$other = \dash\db\sitemap::other();

		if(!is_array($other))
		{
			return;
		}

		self::show_result(__FUNCTION__, count($other));

		foreach ($other as $row)
		{
			$myUrl = $row['url'];
			if($row['language'] && $row['language'] !== self::$default_language)
			{
				$myUrl = $row['language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.5', 'weekly', $row['publishdate']);
		}
	}


	private static function current_project(&$sitemap)
	{
		if(is_callable(['\\lib\\sitemap', 'create']))
		{
			\lib\sitemap::create($sitemap);
		}
	}
}
?>