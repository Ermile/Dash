<?php
namespace content_support\ticket;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("Tickets"));
		\dash\data::page_desc(T_("See list of your tickets!"));
	}
}
?>