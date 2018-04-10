<?php
namespace content_enter\username\set;


class view
{
	public static function config()
	{
		\dash\data::getUsername(\dash\user::login('username'));
		\dash\data::page_title(T_('Set username'));
		\dash\data::page_desc(\dash\data::page_title());
	}

}
?>