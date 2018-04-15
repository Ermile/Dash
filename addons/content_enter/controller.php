<?php
namespace content_enter;


class controller
{

	public static function routing()
	{
		self::check_unlock_page();
		self::always_open_module();
		self::if_login_route();
		self::if_login_not_route();
		self::check_baned_user();
	}


	public static function check_baned_user()
	{
		if(\dash\url::module() !== 'ban')
		{
			$ban = \dash\session::get('enter_baned_user');
			if($ban)
			{
				\dash\utility\enter::next_step('ban');
				\dash\notif::direct();
				\dash\utility\enter::go_to('ban');

			}
		}
	}

	private static function check_unlock_page()
	{
		$need_unlock =
		[
			'alert',
			'ban',
			'block',
			'byebye',
			'email',
			'okay',
			'pass',
			'pass/set',
			'pass/recovery',
			'pass/signup',
			'username',
			'verify',
			'verify/call',
			'verify/email',
			'verify/sendsms',
			'verify/sms',
			'verify/what',
		];

		$check_unlock = \dash\url::module();

		if(\dash\url::child())
		{
			$check_unlock .= '/'. \dash\url::child();
		}

		if(in_array($check_unlock, $need_unlock))
		{
			if(\dash\utility\enter::lock($check_unlock))
			{
				\dash\header::status(404, $check_unlock);
			}
		}
	}


	private static function always_open_module()
	{
		$always_open_modole =
		[
			'signup',
			'hook',
			'google',
			'logout',
			'callback',
			null,
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


	private static function if_login_route()
	{
		$if_login_route =
		[
			'session',
			'delete',
		];

		$module = \dash\url::module();

		if(in_array($module, $if_login_route))
		{
			if(!\dash\user::login())
			{
				\dash\redirect::to(\dash\url::here());
			}
		}
	}


	private static function if_login_not_route()
	{
		$if_login_not_route_module =
		[
			'signup',
			'google',
		];

		$module = \dash\url::module();

		if(in_array($module, $if_login_not_route_module))
		{
			if(\dash\user::login())
			{
				\dash\redirect::to(\dash\url::site());
			}
		}
	}
}
?>