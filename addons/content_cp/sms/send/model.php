<?php
namespace content_cp\sms\send;


class model
{
	public static function post()
	{
		$msg = \dash\request::post('msg');
		if(!$msg)
		{
			\dash\notif::error(T_("No message was sended"), 'msg');
			return false;
		}

		$mobile = \dash\request::get('mobile');

		if(!$mobile)
		{
			$mobile = \dash\request::post('mobile');
		}

		$mobile = \dash\utility\filter::mobile($mobile);

		if(!$mobile)
		{
			\dash\notif::error(T_("Invalid mobile number"), 'mobile');
			return false;
		}

		\dash\utility\sms::send($mobile, $msg);

		\dash\notif::ok("SMS was sended");

	}
}
?>
