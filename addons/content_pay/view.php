<?php
namespace content_pay;

class view
{
	public static function config()
	{
		\dash\data::include_css(false);
		\dash\data::include_js(false);
		\dash\data::include_adminPanel(true);
	}
}
?>