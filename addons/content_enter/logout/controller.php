<?php
namespace content_enter\logout;


class controller
{
	public static function routing()
	{
		// // if user login just can view this page
		// self::if_login_route();

		// get user logout
		\dash\utility\enter::set_logout(\dash\user::id());
	}
}
?>