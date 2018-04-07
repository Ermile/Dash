<?php
namespace content_enter\verify\call;


class controller
{
	public static function routing()
	{
		if(\dash\utility\enter::lock('verify/call'))
		{
			\dash\header::status(404, 'verify/call');
			return;
		}
	}
}
?>