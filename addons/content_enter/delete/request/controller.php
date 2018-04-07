<?php
namespace content_enter\delete\request;

class controller
{
	public static function routing()
	{
		if(\dash\utility\enter::lock('delete/request'))
		{
			\dash\header::status(404, 'delete/request');
		}
	}
}
?>