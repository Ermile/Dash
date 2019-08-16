<?php
namespace content_account\home;

class view
{

	public static function config()
	{
		\dash\data::page_title( \dash\data::site_title(). ' | '. T_("Account"));
		\dash\data::page_desc(T_('Manage your info, privacy, and security to make us work better for you'));
	}
}
?>