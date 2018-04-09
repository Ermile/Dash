<?php
namespace content_enter;

class controller
{
	public static function routing()
	{
		$always_open_modole =
		[
			'signup',
			'hook',
			'google',
			'logout',
			'callback',
		];

		$my_directory = \dash\url::directory();

		if(!in_array($my_directory, $always_open_modole))
		{
			if(\dash\utility\enter::lock($my_directory))
			{
				\dash\header::status(404, $my_directory);
			}
		}


		$if_login_route_module =
		[
			'session',
			'byebye',
			'delete',
		];

		$module = \dash\url::module();

		if(in_array($module, $if_login_route_module))
		{
			if(!\dash\user::login())
			{
				\dash\redirect::to(\dash\url::here());
			}
		}
	}
}
?>