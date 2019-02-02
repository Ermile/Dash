<?php
namespace content_enter\callback;


class model
{
	public static function post()
	{
		switch (\dash\request::get('service'))
		{
			case 'kavenegar':
				self::kavenegar();
				break;

			default:
				\dash\header::status(404, T_("Invalid service"));
				break;
		}
	}

	public static function kavenegar()
	{
		\dash\temp::set('api', true);
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'get'  => \dash\request::get(),
				'post' => \dash\request::post(),
			],
		];

		\dash\db\logs::set('enter:callback:sms:resieve', null, $log_meta);

		$message = \dash\request::post('message');
		$message = trim($message);
		if(!$message || mb_strlen($message) < 1)
		{
			\dash\db\logs::set('enter:callback:message:empty', null, $log_meta);
			\dash\notif::error(T_("Message is empty"));
			return false;
		}


		$mobile = \dash\request::post('from');

		if($mobile)
		{
			$mobile = \dash\utility\filter::mobile($mobile);
		}

		if(!$mobile)
		{
			\dash\db\logs::set('enter:callback:from:not:set', null, $log_meta);
			\dash\notif::error(T_("Mobile not set"));
			return false;
		}

		$user_data = \dash\db\users::get_by_mobile($mobile);

		if(!$user_data || !isset($user_data['id']))
		{
			return self::first_signup_sms();
		}

		$user_id = $user_data['id'];

		$find_log =
		[
			'caller' => 'enterGetSmsFromUser',
			'to'     => $user_id,
			'code'   => $message,
			'status' => 'enable',
		];

		$find_log = \dash\db\logs::get($find_log);

		if(!$find_log || !is_array($find_log) || count($find_log) === 0)
		{
			\dash\db\logs::set('enter:callback:sms:resieve:log:not:found', $user_id, $log_meta);
			\dash\notif::error(T_("Log not found"));
			return false;
		}

		if(count($find_log) > 1)
		{
			\dash\db\logs::set('enter:callback:sms:more:than:one:log:found', $user_id, $log_meta);
			foreach ($find_log as $key => $value)
			{
				if(isset($value['id']))
				{
					\dash\db\logs::update(['status' => 'expire'], $value);
				}
			}
			\dash\notif::error(T_("More than one log found"));
			return false;
		}


		if(count($find_log) === 1)
		{
			$find_log = $find_log[0];
			if(isset($find_log['id']))
			{
				\dash\db\logs::update(['status' => 'deliver'], $find_log['id']);
				\dash\notif::ok(T_("OK"));
				return true;
			}
		}

		// {
		// 	"get":"service=kavenegar&type=recieve&uid=201700001",
		// 	"post":
		// 		{
		// 			"messageid":"308404060",
		// 			"from":"09109610612",
		// 			"to":"10006660066600",
		// 			"message":"Salamq"
		// 		}
		// 	}
	}


	/**
	 * singup user and send the regirster sms to he
	 */
	public static function first_signup_sms()
	{
		$mobile = \dash\request::post('from');

		if($mobile)
		{
			$mobile = \dash\utility\filter::mobile($mobile);
		}

		if(!$mobile)
		{
			\dash\notif::error(T_("Mobile not set"));
			return false;
		}

		$signup =
		[
			'mobile'      => $mobile,
			'password'    => null,
			'displayname' => null,
			'datecreated' => date("Y-m-d H:i:s"),
		];

		$user_id = \dash\db\users::signup($signup);


		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'get'  => \dash\request::get(),
				'post' => \dash\request::post(),
			],
		];

		\dash\db\logs::set('enter:callback:signup:by:sms', $user_id, $log_meta);

		$msg    = T_("Your register was complete");

		// $kavenegar_send_result = \dash\utility\sms::send($mobile, $msg);

		// $log_meta['meta']['register_sms_result'] = $kavenegar_send_result;

		// \dash\db\logs::set('enter:callback:sms:registe:reasult', $user_id, $log_meta);

		\dash\notif::ok(T_("User signup by sms"));


	}
}
?>