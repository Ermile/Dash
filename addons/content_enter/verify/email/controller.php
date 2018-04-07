<?php
namespace content_enter\verify\email;


class controller
{
	public static function routing()
	{
		if(\dash\utility\enter::lock('verify/email'))
		{
			\dash\header::status(404, 'verify/email');
		}
	}
}
?>