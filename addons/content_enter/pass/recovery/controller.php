<?php
namespace content_enter\pass\recovery;


class controller
{
	public static function routing()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('pass/recovery'))
		{
			\dash\header::status(404, 'pass/recovery');
		}
	}
}
?>