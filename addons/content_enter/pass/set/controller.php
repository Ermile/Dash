<?php
namespace content_enter\pass\set;

class controller
{
	public static function routing()
	{
		// if step mobile is done
		if(\dash\utility\enter::user_data('password'))
		{
			\dash\header::status(404, 'pass/set');
		}
	}
}
?>