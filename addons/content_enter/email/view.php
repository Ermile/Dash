<?php
namespace content_enter\email;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('Enter to :name with email', ['name' => \dash\data::site_title()]));
		\dash\data::page_special(true);
		\dash\data::page_desc(\dash\data::page_title());
	}
}
?>