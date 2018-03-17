<?php
namespace addons\content_enter\verify;


class model extends \addons\content_enter\main\model
{
	public function post_verify_way()
	{
		$mobile_email = \lib\request::post('usernameormobile');
		$send_code    = mb_strtolower(\lib\request::post('sendCod'));

		$exist_mobile_email = $this->view()->data->get_usernamemobile;
		if($mobile_email !== $exist_mobile_email)
		{
			\lib\notif::error(T_("What are you doing?"));
			return false;
		}

		if(!in_array($send_code, self::list_send_code_way($mobile_email)))
		{
			\lib\notif::error(T_("Dont!"));
			return false;
		}

		if(!self::get_enter_session('code_is_created'))
		{
			self::set_enter_session('code_is_created', true);
			self::send_way();
		}

		if(\lib\url::isLocal())
		{
			\lib\notif::true(T_("Verify code in local is :code", ['code' => '<b>11111</b>']));
		}

		$select_way = 'verify/'. $send_code;
		self::open_lock($select_way);
		self::go_to($select_way);

	}
}
?>