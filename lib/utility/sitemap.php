<?php
namespace dash\utility;


class sitemap
{
	private static $set_result      = [];
	private static $current_language = null;
	private static $default_language = null;
	private static $count_all = 0;

	public static function addr()
	{
		return root. 'public_html/';
	}

	public static function folder_name()
	{
		return 'sitemap';
	}

	public static function folder_addr()
	{
		return root. 'public_html/sitemap/';
	}

	public static function file_addr()
	{
		return root. 'public_html/sitemap.xml';
	}

	public static function new_sitemap()
	{
		$site_url = \dash\url::site().'/';
		return new \dash\utility\sitemap_generator($site_url , \dash\utility\sitemap::addr(), \dash\utility\sitemap::folder_name());
	}


	public static function delete()
	{
		$count = 0;
		$dir = \dash\utility\sitemap::folder_addr();
		if(is_dir($dir))
		{
			$files = glob($dir. '*');
			if(is_array($files))
			{
				foreach ($files as $key => $value)
				{
					\dash\file::delete($value);
					$count++;
				}
			}

		}

		$file = \dash\utility\sitemap::file_addr();
		if(is_file($file))
		{
			\dash\file::delete($file);
			$count++;
		}

		\dash\session::set('result_create_sitemap' , null);
		\dash\notif::ok(\dash\utility\human::fitNumber($count). ' '. T_("File removed"));
		return true;
	}

	public static function plus_count_all()
	{
		self::$count_all++;
	}


	public static function create()
	{
		// set log to create new sitemap
		\dash\log::set('sitemapGenerate');

		// make new set_result
		self::$set_result = [];

		self::$current_language = \dash\language::current();
		self::$default_language = \dash\language::primary();

		// create sitemap for each language
		self::static_page();

		self::add_sitemap_item('posts', 		\dash\db\sitemap::posts(), 		'0.8', 'daily', 	'publishdate');
		self::add_sitemap_item('pages', 		\dash\db\sitemap::pages(), 		'0.8', 'daily', 	'publishdate');
		self::add_sitemap_item('mags',  		\dash\db\sitemap::mags(), 		'0.8', 'daily', 	'publishdate');
		self::add_sitemap_item('attachments',  	\dash\db\sitemap::attachments(),'0.2', 'weekly', 	'publishdate');
		self::add_sitemap_item('help_center',  	\dash\db\sitemap::help_center(),'0.3', 'monthly', 	'publishdate');
		self::add_sitemap_item('other',  		\dash\db\sitemap::other(), 		'0.5', 'weekly', 	'publishdate');
		self::add_sitemap_item('mag_tag',  		\dash\db\sitemap::mag_tag(), 	'0.5', 'weekly', 	'datecreated', 'tag');
		self::add_sitemap_item('help_tag',  	\dash\db\sitemap::help_tag(), 	'0.5', 'weekly', 	'datecreated', 'tag');
		self::add_sitemap_item('mag_cat',  		\dash\db\sitemap::mag_cat(), 	'0.5', 'weekly', 	'datecreated', 'cat');
		self::add_sitemap_item('cats',  		\dash\db\sitemap::cats(), 		'0.5', 'weekly', 	'datecreated', 'cat');
		self::add_sitemap_item('tags',  		\dash\db\sitemap::tags(), 		'0.5', 'weekly', 	'datecreated', 'tag');

		self::current_project();

		self::sitemapIndex();

		\dash\notif::info(\dash\utility\human::fitNumber(self::$count_all). ' '. T_("Link created"));

		return self::$set_result;

	}

	// can call from current project to show result
	// \lib\sitemap::create();
	public static function set_result($_key, $_count)
	{
		self::$set_result[$_key] = $_count;

	}

	private static function sitemapIndex()
	{
		if(!empty(self::$set_result))
		{
			$site_url = \dash\url::site().'/';
			$sitemap  = self::new_sitemap();
			$sitemap->makeSitemapIndex(array_keys(self::$set_result));

		}
	}

	private static function static_page()
	{
		$site_url = \dash\url::site().'/';
		$sitemap  = self::new_sitemap();
		$sitemap->setFilename('static_page');

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

		self::set_result('static_page', count($static_page));

		$sitemap->endSitemap();
	}


	private static function add_sitemap_item($_type, $_array, $_priority, $_changefreq, $_lastmod_field, $_url_word = null)
	{
		if(!is_array($_array) || !$_array)
		{
			self::set_result($_type, 0);
			return;
		}

		$site_url = \dash\url::site().'/';
		$sitemap  = self::new_sitemap();

		$sitemap->setFilename($_type);

		foreach ($_array as $row)
		{
			$myUrl = $row['url'];

			if($_url_word)
			{
				$myUrl = $_url_word. '/'. $myUrl;
			}

			if($row['language'] && $row['language'] !== self::$default_language)
			{
				$myUrl = $row['language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, $_priority, $_changefreq, $row[$_lastmod_field]);
		}


		$sitemap->endSitemap();

		self::set_result($_type, count($_array));
	}


	private static function current_project()
	{
		if(is_callable(['\\lib\\sitemap', 'create']))
		{
			\lib\sitemap::create();
		}
	}
}
?>