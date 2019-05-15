<?php
namespace content_api;


class controller
{
	public static function routing()
	{
		// save api log
		\dash\app\apilog::start();
	}
}
?>