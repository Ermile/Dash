<?php
namespace content_enter\pass\set;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_('set mobile number'));
		\dash\data::page_desc(\dash\data::page_title());
	}
}
?>