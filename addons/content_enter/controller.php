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
	}
}
?>