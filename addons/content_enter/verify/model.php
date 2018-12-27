<?php
namespace content_enter\verify;


class model
{
	public static function post()
	{
		$mobile_email = \dash\request::post('usernameormobile');
		$send_code    = mb_strtolower(\dash\request::post('sendCod'));

		$exist_mobile_email = \dash\utility\enter::get_session('usernameormobile');

		if(!$exist_mobile_email && \dash\user::login())
		{
			if(\dash\user::detail('mobile'))
			{
				$exist_mobile_email = \dash\utility\filter::mobile(\dash\user::detail('mobile'));
			}
			elseif(\dash\user::detail('username'))
			{
				$exist_mobile_email = \dash\user::detail('username');
			}
			elseif(\dash\user::detail('email'))
			{
				$exist_mobile_email = \dash\user::detail('email');
			}
		}

		if($mobile_email !== $exist_mobile_email)
		{
			if(\dash\utility\filter::mobile($mobile_email) !== \dash\utility\filter::mobile($exist_mobile_email))
			{
				\dash\log::set('existMobileIsNotMathcBySendMobile');

				\dash\notif::error(T_("What are you doing?"));
				return false;
			}
		}

		if(!$send_code)
		{
			\dash\notif::error(T_("Please select one way to send code"), 'sendCod');
			return false;
		}

		if(!in_array($send_code, \dash\utility\enter::list_send_code_way()))
		{
			\dash\log::set('sendWayInvalid');
			\dash\notif::error(T_("Please select one way to send code"));
			return false;
		}

		if($send_code !== 'later')
		{
			\dash\utility\enter::generate_verification_code();
		}

		if(\dash\url::isLocal() && $send_code !== 'later')
		{
			\dash\notif::ok(T_("Verify code in local is :code", ['code' => '<b>11111</b>']));
		}

		if($send_code !== 'later' && \dash\utility\enter::get_session('verify_from') === 'signup')
		{
			$signup = \dash\utility\enter::get_session('signup_detail');

			if(!$signup || !is_array($signup))
			{
				\dash\log::set('userDetailLostSignup');
				\dash\notif::error(T_("We can not find your detail to signup"));
				return false;
			}

			$user_id = \dash\db\users::signup($signup);

			if(!$user_id)
			{
				\dash\log::set('userCanNotSignupDB');
				\dash\notif::error(T_("We can not signup you"));
				return false;
			}

			// load user data by mobile
			$user_data = \dash\utility\enter::load_user_data($user_id, 'user_id');

			\dash\log::set('userSignup');

		}


		$select_way = 'verify/'. $send_code;
		\dash\utility\enter::open_lock($select_way);
		\dash\utility\enter::go_to($select_way);
	}
}
?>