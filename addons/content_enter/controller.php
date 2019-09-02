<?php
namespace content_enter;


class controller
{

	public static function routing()
	{

		if(\dash\option::config('no_subdomain'))
		{
			\dash\redirect::remove_subdomain();
		}

		self::check_block_cookie();

		self::check_unlock_page();
		self::if_login_route();
		self::if_login_not_route();
		self::check_baned_user();

		// save referer
		// to redirect the user ofter login or signup on the referered address
		if(\dash\request::get('referer') && \dash\request::get('referer') != '')
		{
			$_SESSION['enter_referer'] = \dash\request::get('referer');
		}
	}

	public static function check_block_cookie()
	{
		if(empty($_SESSION) || !$_SESSION)
		{
			if(\dash\url::module() !== 'app')
			{
				\dash\notif::warn(T_("Your cookies may have been blocked"). ' '. T_("You need to enable cookie for usign this service"));
			}
			$_SESSION['your_cookie_is_blocked'] = true;
		}
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
			'verify/telegram',
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

				if($check_unlock === 'okay')
				{
					\dash\redirect::to(\dash\url::kingdom());
				}
				else
				{
					if(substr($check_unlock, 0,6) === 'verify')
					{
						if(\dash\user::id() && !\dash\user::detail('verifymobile'))
						{
							// no problem to load this page
						}
						else
						{
							\dash\header::status(403, $check_unlock);
						}
					}
					else
					{
						\dash\header::status(403, $check_unlock);
					}
				}
			}
		}
	}




	private static function if_login_route()
	{
		$if_login_route =
		[
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