<?php
namespace content_api\v6\enter;


trait login
{


	private static function login()
	{
		$check_input = self::check_input();
		if(!$check_input)
		{
			return false;
		}

		$check_true = self::check_true_user();
		if(!$check_true)
		{
			return false;
		}

		$user_id = \dash\db\users::signup(['mobile' => self::$mobile]);
		if(!$user_id)
		{
			\dash\log::set('API-canNotSignupUserEnter');
			\dash\notif::error(T_("Can not signup this mobile"));
			return false;
		}

		self::$mobile_user_id = $user_id;

		$check_log =
		[
			'caller' => 'enter_apiverificationcode',
			'to'     => $user_id,
			'limit'  => 1,
		];

		$check_log = \dash\db\logs::get($check_log, ['order' => 'ORDER BY logs.id DESC']);

		$generate_new_code = false;

		if(!isset($check_log['id']))
		{
			$generate_new_code = true;
		}
		else
		{
			// 'enable','disable','expire','deliver','awaiting','deleted','cancel','block','notif','notifread','notifexpire'
			if(isset($check_log['status']) && in_array($check_log['status'], ['enable', 'notif', 'notifread']))
			{
				if(isset($check_log['datecreated']))
				{
					$old_time = strtotime($check_log['datecreated']);
					if((time() - $old_time) > self::$life_time)
					{
						$generate_new_code = true;
					}
				}
				else
				{
					$generate_new_code = true;
				}
			}
			else
			{
				$generate_new_code = true;
			}
		}


		if($generate_new_code)
		{
			$myCode = rand(10000, 99999);

			$log =
			[
				'to'     => $user_id,
				'code'   => $myCode,
				'mycode' => $myCode,
			];

			\dash\log::set('enter_apiverificationcode', $log);
			\dash\notif::ok(T_("The verification code sended to phone number"));
			return true;
		}
		else
		{
			\dash\notif::error(T_("A verification code was sended to user"));
			return false;
		}
	}
}
?>