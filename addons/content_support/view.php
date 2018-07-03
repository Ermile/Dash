<?php
namespace content_support;

class view
{
	public static function config()
	{
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);
		\dash\data::include_js(false);

		\dash\data::include_chart(true);
		\dash\data::display_admin('content_support/layout.html');
	}
}
?>