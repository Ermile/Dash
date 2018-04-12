<?php
namespace content_account\home;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("Dashboard"));
		\dash\data::page_desc(T_("Account of your user to change profile details or change billing information"));
	}
}
?>