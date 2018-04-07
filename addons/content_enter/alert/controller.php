<?php
namespace content_enter\alert;


class controller
{
	public static function routing()
	{
		if(\dash\utility\enter::lock('alert'))
		{
			\dash\header::status(404, 'alert');
		}
	}
}
?>