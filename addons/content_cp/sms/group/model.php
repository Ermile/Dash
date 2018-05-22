<?php
namespace content_cp\sms\group;


class model
{
	public static function post()
	{
		$template_post     = \dash\request::post('template');
		$usersmobile = \dash\request::post('usersmobile');

		if(\dash\request::post('changeTemplate'))
		{
			$query             = [];

			$query['template'] = $template_post;
			if($usersmobile)
			{
				\dash\session::set('usersmobile_sms', $usersmobile);
			}

			\dash\redirect::to(\dash\url::this(). '/group?'. http_build_query($query));

			return;
		}

		$msg = \dash\request::post('msg');
		if(!$msg)
		{
			\dash\notif::error(T_("No message was sended"), 'msg');
			return false;
		}

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
