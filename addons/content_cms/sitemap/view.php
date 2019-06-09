<?php
namespace content_cms\sitemap;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_('Sitemap'));

		$result = \dash\session::get('result_create_sitemap');
		\dash\data::sitemapData($result);

		if(is_dir(root. '/public_html/sitemap'))
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