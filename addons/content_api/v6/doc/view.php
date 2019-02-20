<?php
namespace content_api\v6\doc;

class view
{
	public static function config()
	{
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);
		\dash\data::include_js(false);

		\dash\data::page_title( T_(':val API documentation v6', ['val' => \dash\data::site_title()]));
		\dash\data::page_desc(T_('Last modified'). ' '. \dash\datetime::fit('2019-02-20 18:40', 'human', 'year'));
	}
}
?>