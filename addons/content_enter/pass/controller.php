<?php
namespace content_enter\pass;


class controller
{
	public static function routing()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('pass'))
		{
			\dash\header::status(404, 'pass');
			return;
		}
	}
}
?>