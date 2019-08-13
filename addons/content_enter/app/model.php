<?php
namespace content_enter\app;


class model
{

	public static function post()
	{
		$logout = \dash\request::post('logoutapp');
		if(\dash\user::id() && $logout)
		{
			\dash\utility\enter::set_logout(\dash\user::id(), false);
			\dash\redirect::pwd();
			return;
		}

		$mobile = \dash\request::post('usernameormobile');
		$mobile = \dash\utility\convert::to_en_number($mobile);
		$mobile = \dash\utility\filter::mobile($mobile);
		if(!$mobile)
		{
			\dash\notif::error(T_("Invalid mobile"), 'usernameormobile');
			return false;
		}

		$count = \dash\session::get('count_try_to_login_app', 'enter');
		if($count)
		{
			\dash\session::set('count_try_to_login_app', $count + 1, 'enter', 60 * 3);
		}
		else
		{
			\dash\session::set('count_try_to_login_app', 1, 'enter', 60 * 3);
		}

		$anotherPerm = \dash\permission::check('EnterByAnother');
		if($count >= 3 && !$anotherPerm)
		{
			\dash\log::set('try3>in60sAppMode');
			\dash\notif::error(T_("You hit our maximum try limit."). ' '. T_("Try again later!"));
			return false;
		}

		// clean existing session
		\dash\utility\enter::clean_session();

		// set posted mobile in SESSION
		\dash\utility\enter::set_session('usernameormobile', $mobile);


		// load user data by mobile
		$user_data = \dash\utility\enter::load_user_data($mobile, 'mobile');

		// the user not found must be signup
		if(!$user_data)
		{
			$user_id   = \dash\db\users::signup(['mobile' => $mobile]);
			$user_data = \dash\utility\enter::load_user_data($user_id, 'user_id');

			if(!$user_data)
			{
				\dash\log::set('appCanNotSignupUser');
				\dash\notif::error(T_("We can not signup you"));
				return false;
			}
		}

		// if this user is blocked or filtered go to block page
		if(in_array(\dash\utility\enter::user_data('status'), ['filter', 'block']))
		{
			\dash\log::set('statusBlockOrFilter');

			\dash\utility\enter::try('login_status_block');
			// block page
			\dash\utility\enter::next_step('block');
			// go to block page
			\dash\utility\enter::go_to('block');
			return;
		}

		\dash\utility\enter::set_session('app_mode', true);

		// lock all step and set just this page to load
		\dash\utility\enter::next_step('verify');

		// go to pass to check password
		\dash\utility\enter::go_to('verify');


	}
}
?>