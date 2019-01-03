<?php
namespace content_hook;

class view
{
	public static function config()
	{

		\dash\data::include_adminPanel(false);
		\dash\data::include_css(false);
		\dash\data::include_js(false);
		\dash\data::include_highcharts(false);
		\dash\data::include_editor(false);
	}
}
?>