<?php
namespace content_enter\verify\sms;


class view
{
	public static function config()
	{
		if(!\dash\utility\enter::get_session('run_send_sms_code'))
		{
			\dash\utility\enter::set_session('run_send_sms_code', true);
			\content_enter\verify\sms\model::send_sms_code();
		}
	}
}
?>