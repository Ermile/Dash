<?php
namespace content_enter\ban;


class controller
{
	public static function routing()
	{
		if(\dash\utility\enter::lock('ban'))
		{
			\dash\header::status(404, 'ban');
		}
	}
}
?>