<?php
namespace content_enter\verify;


class model extends \addons\content_enter\main\model
{
	public function post_verify_way()
	{
		$mobile_email = \dash\request::post('usernameormobile');
		$send_code    = mb_strtolower(\dash\request::post('sendCod'));

		$exist_mobile_email = \dash\data::getUsernamemobile();
		if($mobile_email !== $exist_mobile_email)
		{
			\dash\notif::error(T_("What are you doing?"));
			return false;
		}

		if(!in_array($send_code, self::list_go_to_verify($mobile_email)))
		{
			\dash\notif::error(T_("Dont!"));
			return false;
		}

		if(!\dash\utility\enter::get_session('code_is_created'))
		{
			\dash\utility\enter::set_session('code_is_created', true);
			// self::send_way();
		}

		if(\dash\url::isLocal())
		{
			\dash\notif::ok(T_("Verify code in local is :code", ['code' => '<b>11111</b>']));
		}

		$select_way = 'verify/'. $send_code;
		self::open_lock($select_way);
		\dash\utility\enter::go_to($select_way);

	}
}
?>