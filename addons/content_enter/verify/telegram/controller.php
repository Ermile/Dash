<?php
namespace content_enter\verify\telegram;


class controller
{
	public static function routing()
	{
		if(\dash\utility\enter::lock('verify/telegram'))
		{
			\dash\header::status(404, 'verify/telegram');
		}
	}
}
?>