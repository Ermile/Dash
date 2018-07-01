<?php
namespace content_su\command;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Command"));
		\dash\data::page_desc(T_('Run some command in server'));

	}
}
?>