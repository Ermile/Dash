<?php
namespace content_hook\pay\token;


class model
{
	public static function post()
	{
		\dash\utility\pay\start::token(\dash\request::post());
	}
}
?>