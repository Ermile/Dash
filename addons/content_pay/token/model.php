<?php
namespace content_pay\token;


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