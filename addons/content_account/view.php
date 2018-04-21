<?php
namespace content_account;

class view
{
	public static function config()
	{
		\dash\data::bodyclass('siftal');
		\dash\data::include_css(false);
		\dash\data::include_js(false);

		\dash\data::include_chart(true);
		\dash\data::display_admin('content_account/layout.html');
	}
}
?>