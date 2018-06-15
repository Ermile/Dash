<?php
namespace content_su\home;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Supervisor dashboard"));
		\dash\data::page_desc(T_("Hey there!"));

		\dash\data::page_pictogram('gauge');
	}
}
?>