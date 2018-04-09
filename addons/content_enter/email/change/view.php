<?php
namespace content_enter\email\change;

class view
{
	public static function config()
	{
		\dash\data::getEmail(\dash\user::login('email'));
		\dash\data::page_title(T_('Change email'));
		\dash\data::page_desc(\dash\data::page['title']);
	}
}
?>