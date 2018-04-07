<?php
namespace content_enter\okay;


class controller
{
	public static function routing()
	{
		if(\dash\utility\enter::lock('okay'))
		{
			\dash\header::status(404, 'okay');
		}
	}
}
?>