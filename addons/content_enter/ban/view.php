<?php
namespace content_enter\ban;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('You are Banned!!'));
		\dash\data::page_special(true);
		\dash\data::page_desc(\dash\data::page_title());
	}
}
?>