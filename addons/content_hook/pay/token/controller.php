<?php
namespace content_hook\pay\token;


class controller
{
	public static function routing()
	{
		if(\dash\request::is('post'))
		{
			// noproblem
		}
		else
		{
			\dash\header::status(400);
		}
	}
}
?>