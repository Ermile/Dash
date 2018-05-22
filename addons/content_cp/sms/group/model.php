<?php
namespace content_cp\sms\group;


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

		$usersmobile = \dash\request::post('usersmobile');
		if(!$usersmobile)
		{
			\dash\notif::error(T_("Please fill the mobiles field"), 'usersmobile');
			return false;
		}

		$mobile = [];
		$split = explode("\n", $usersmobile);
		foreach ($split as $key => $value)
		{
			$value = trim($value);
			$temp = \dash\utility\filter::mobile($value);

			if($temp)
			{
				$mobile[] = $temp;
			}
		}

		$mobile = array_filter($mobile);
		$mobile = array_unique($mobile);

		if(!$mobile)
		{
			\dash\notif::error(T_("No valid mobile find in your list"), 'usersmobile');
			return false;
		}

		\dash\utility\sms::send_array($mobile, $msg);
		\dash\notif::ok(T_("SMS was sended to :count unique mobile number", ['count' => count($mobile)]));

	}
}
?>
