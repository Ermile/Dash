<?php
namespace content_crm\sms\group;


class model
{
	public static function post()
	{
		\dash\permission::access('cpSmsSendGroup');
		$template_post     = \dash\request::post('template');
		$usersmobile = \dash\request::post('usersmobile');

		if(\dash\request::post('changeTemplate'))
		{
			$query             = [];
			$query['template'] = $template_post;
			$query['group']    = \dash\request::post('group');
			if($usersmobile)
			{
				\dash\session::set('usersmobile_sms', $usersmobile);
			}

			\dash\redirect::to(\dash\url::this(). '/group?'. http_build_query($query));

			return;
		}

		$mobile = [];
		$group = \dash\request::post('group');
		if($group)
		{
			$group = \dash\app\smsgroup::get($group);
			if($group === false)
			{
				return false;
			}
			$mobile = $group;
		}

		$msg = \dash\request::post('msg');
		if(!$msg)
		{
			\dash\notif::error(T_("No message was sended"), 'msg');
			return false;
		}

		$usersmobile = \dash\request::post('usersmobile');
		$split       = explode("\n", $usersmobile);
		$mobile      = array_merge($mobile, $split);

		foreach ($mobile as $key => $value)
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

		$send           = [];
		$send['msg']    = $msg;
		$send['mobile'] = $mobile;

		\dash\session::set('verify_sms_send', $send);

		\dash\redirect::to(\dash\url::this().'/verify');

	}
}
?>
