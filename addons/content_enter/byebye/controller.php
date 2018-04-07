<?php
namespace content_enter\byebye;


class controller
{
	public static function routing()
	{
		if(\dash\utility\enter::lock('byebye'))
		{
			\dash\header::status(404, 'byebye');
		}
	}
}
?>