<?php
namespace content_enter\pass\set;

class controller
{
	public static function routing()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('pass/set'))
		{
			\dash\header::status(404, 'pass/set');
		}

		// if step mobile is done
		if(\dash\utility\enter::user_data('password'))
		{
			\dash\header::status(404, 'pass/set');
		}
	}
}
?>