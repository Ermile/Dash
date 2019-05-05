<?php
namespace content_cms\cms;

class view
{
	public static function config()
	{
		\dash\data::page_pictogram('diamond');
		\dash\data::page_title(T_("CMS Dashboard"));
		\dash\data::page_desc(T_("CMS Dashboard"));
	}
}
?>