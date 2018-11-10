<?php
namespace content_support\ticket\contact_ticket;

class controller
{
	public static function routing()
	{
		\dash\utility\ip::check();

		\dash\utility\hive::set();
	}
}
?>
