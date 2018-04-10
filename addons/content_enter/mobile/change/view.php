<?php
namespace content_enter\pass\change;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('change mobile number'));
		\dash\data::page_desc(\dash\data::page_title());
	}
}
?>