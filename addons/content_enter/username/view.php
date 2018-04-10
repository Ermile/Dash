<?php
namespace content_enter\username;


class view
{
	public static function config()
	{

		\dash\data::page_title(T_('Enter to :name with username', ['name' => \dash\data::site_title()]));
		\dash\data::page_special(true);
		\dash\data::page_desc(\dash\data::page_title());
	}
}
?>