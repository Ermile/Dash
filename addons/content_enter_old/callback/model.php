<?php
namespace addons\content_enter\callback;


class model extends \addons\content_enter\main\model
{
	public function kavenegar()
	{
		\lib\temp::set('api', true);
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'get'  => \lib\request::get(),
				'post' => \lib\request::post(),
			],
		];

		\lib\db\logs::set('enter:callback:sms:resieve', null, $log_meta);

		$message = \lib\request::post('message');
		$message = trim($message);
		if(!$message || mb_strlen($message) < 1)
		{
			\lib\db\logs::set('enter:callback:message:empty', null, $log_meta);
			\lib\notif::error(T_("Message is empty"));
			return false;
		}


		$mobile = \lib\request::post('from');

		if($mobile)
		{
			$mobile = \lib\utility\filter::mobile($mobile);
		}

		if(!$mobile)
		{
			\lib\db\logs::set('enter:callback:from:not:set', null, $log_meta);
			\lib\notif::error(T_("Mobile not set"));
			return false;
		}

		$user_data = \lib\db\users::get_by_mobile($mobile);

		if(!$user_data || !isset($user_data['id']))
		{
			return $this->first_signup_sms();
		}

		$user_id = $user_data['id'];

		$find_log =
		[
			'caller'     => 'enter:get:sms:from:user',
			'user_id'    => $user_id,
			'data'   => $message,
			'status' => 'enable',
		];

		$find_log = \lib\db\logs::get($find_log);

		if(!$find_log || !is_array($find_log) || count($find_log) === 0)
		{
			\lib\db\logs::set('enter:callback:sms:resieve:log:not:found', $user_id, $log_meta);
			\lib\notif::error(T_("Log not found"));
			return false;
		}

		if(count($find_log) > 1)
		{
			\lib\db\logs::set('enter:callback:sms:more:than:one:log:found', $user_id, $log_meta);
			foreach ($find_log as $key => $value)
			{
				if(isset($value['id']))
				{
					\lib\db\logs::update(['status' => 'expire'], $value);
				}
			}
			\lib\notif::error(T_("More than one log found"));
			return false;
		}


		if(count($find_log) === 1)
		{
			$find_log = $find_log[0];
			if(isset($find_log['id']))
			{
				\lib\db\logs::update(['status' => 'deliver'], $find_log['id']);
				\lib\notif::ok(T_("OK"));
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
	public function first_signup_sms()
	{
		$mobile = \lib\request::post('from');

		if($mobile)
		{
			$mobile = \lib\utility\filter::mobile($mobile);
		}

		if(!$mobile)
		{
			\lib\notif::error(T_("Mobile not set"));
			return false;
		}

		$signup =
		[
			'mobile'      => $mobile,
			'password'    => null,
			'displayname' => null,
			'datecreated' => date("Y-m-d H:i:s"),
		];

		\lib\db\users::insert($signup);
		$user_id = \lib\db::insert_id();


		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'get'  => \lib\request::get(),
				'post' => \lib\request::post(),
			],
		];

		\lib\db\logs::set('enter:callback:signup:by:sms', $user_id, $log_meta);

		$msg    = T_("Your register was complete");

		$kavenegar_send_result = \lib\utility\sms::send($mobile, $msg);

		$log_meta['meta']['register_sms_result'] = $kavenegar_send_result;

		\lib\db\logs::set('enter:callback:sms:registe:reasult', $user_id, $log_meta);

		\lib\notif::ok(T_("User signup by sms"));


	}
}
?>