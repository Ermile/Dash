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
				\dash\notif::error(T_("What are you doing?"));
				return false;
			}
		}

		if(!in_array($send_code, \dash\utility\enter::list_send_code_way($mobile_email)))
		{
			\dash\notif::error(T_("Dont!"));
			return false;
		}

		if(\dash\url::isLocal())
		{
			\dash\notif::ok(T_("Verify code in local is :code", ['code' => '<b>11111</b>']));
		}

		$select_way = 'verify/'. $send_code;
		\dash\utility\enter::open_lock($select_way);
		\dash\utility\enter::go_to($select_way);
	}
}
?>