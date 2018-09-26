<?php
namespace content_enter\byebye;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('Come back to us!'));
		\dash\data::page_desc(\dash\data::page_title());
		//put logout
		\dash\log::set('userByeBye');
		\dash\utility\enter::set_logout(\dash\user::id(), false);
	}
}
?>