<?php
namespace content_enter\twostep;


class model
{
	public static function post()
	{
		if(!\dash\user::login())
		{
			\dash\notif::error(T_("Please login to continue"));
			return false;
		}

		$action = \dash\request::post('action');
		if(!in_array($action, ['active', 'deactive']))
		{
			\dash\notif::error(T_("Invalid action"));
			return false;
		}

		if(\dash\user::detail('twostep') && $action === 'active')
		{
			\dash\notif::ok(T_("Two-step verification for you is already active"));
			return true;

		}

		if(!\dash\user::detail('twostep') && $action === 'deactive')
		{
			\dash\notif::warn(T_("Two-step verification for you is already deactive"));
			return true;
		}

		\dash\utility\enter::load_user_data(\dash\user::detail(), 'loaded');

		if($action === 'active')
		{
			\dash\utility\enter::set_session('verify_from', 'two_step_set');
		}
		else
		{
			\dash\utility\enter::set_session('verify_from', 'two_step_unset');
		}

		// send code way
		\dash\utility\enter::go_to_verify();


	}
}
?>