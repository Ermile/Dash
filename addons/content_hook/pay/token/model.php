<?php
namespace content_hook\pay\token;


class model
{
	public static function post()
	{
		if(\dash\user::login())
		{
			\dash\utility\pay\start::token(\dash\request::post());
		}
	}
}
?>