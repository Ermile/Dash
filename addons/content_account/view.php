<?php
namespace content_account;

class view
{
	public static function config()
	{
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);
		\dash\data::include_js(false);

		\dash\data::include_editor(true);

		\dash\data::display_admin('content_account/layout.html');

		// \dash\data::site_title(T_(":site Account", ['site' => \dash\data::site_title()]));
	}
}
?>