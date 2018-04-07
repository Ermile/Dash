<?php
namespace content_enter\block;


class controller
{
	public static function routing()
	{
		if(\dash\utility\enter::lock('block'))
		{
			\dash\header::status(404, 'block');
		}
	}
}
?>