<?php
namespace content_support\ticket\show;

class controller
{
	public static function routing()
	{
		\dash\utility\ip::check(true);

		\dash\utility\hive::set();
	}
}
?>
