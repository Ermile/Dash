<?php
namespace content_enter\email\set;

class view
{
	public static function config()
	{
		\dash\data::getEmail(\dash\user::login('email'));
		\dash\data::page_title(T_('set email'));
		\dash\data::page_desc(\dash\data::page_title());
	}

}
?>