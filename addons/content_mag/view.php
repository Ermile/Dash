<?php
namespace content_mag;

class view
{
	public static function config()
	{
		// define default value for global
		\dash\data::site_title(T_("Magazine"));
		\dash\data::site_desc(null);
		\dash\data::site_slogan(null);
		\dash\data::page_title(\dash\data::site_title());
		\dash\data::page_desc(\dash\data::site_desc(). ' | '. \dash\data::site_slogan());
		\dash\data::page_special(true);

	}
}
?>