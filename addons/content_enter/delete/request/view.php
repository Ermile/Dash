<?php
namespace content_enter\delete\request;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('Request delete account'));
		\dash\data::page_desc(\dash\data::page_title());
	}
}
?>