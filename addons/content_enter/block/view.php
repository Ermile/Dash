<?php
namespace content_enter\block;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('Hey! You are Blocked!!'));
		\dash\data::page_desc(\data\data::page_title());
	}
}
?>