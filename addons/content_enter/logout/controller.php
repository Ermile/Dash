<?php
namespace content_enter\logout;


class controller
{
	public static function routing()
	{
		// get user logout
		\dash\utility\enter::set_logout(\dash\user::id());
	}
}
?>