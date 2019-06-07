<?php
namespace content_cms\sitemap;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_('Sitemap'));

		if(\dash\request::get('run') === 'yes')
		{
			\dash\data::sitemapData(\dash\utility\sitemap::create());
		}

		if(is_file(root. '/public_html/sitemap/sitemap.xml'))
		{
			\dash\data::siteMapFile_sitemap(true);
		}

		if(is_file(root. '/public_html/sitemap.xml'))
		{
			\dash\data::siteMapFile_base(true);
		}
	}
}
?>