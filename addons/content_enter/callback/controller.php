<?php
namespace content_enter\callback;


class controller
{
	public static function routing()
	{
		// 10002000200251
		if(!\dash\request::get('service') || \dash\request::get('uid') != '201708111')
		{
			\dash\header::status(404, T_("Invalid url"));
		}

		\dash\log::set('smsCallback');


	}
}
?>