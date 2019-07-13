<?php
namespace content_api\v6\enter;


class enter
{


	private static $life_time      = 60 * 2;
	private static $verifycode     = null;
	private static $mobile         = null;
	private static $user_id        = null;
	private static $mobile_user_id = null;
	private static $response       = null;


	public static function fire()
	{
		\content_api\v6::check_token();

		\content_api\v6::check_apikey();

		$subchild = \dash\url::subchild();

		if(!$subchild)
		{
			self::login();
		}
		elseif($subchild === 'verify')
		{
			self::verify();
		}
		else
		{
			\content_api\v6::no(404);
		}

		if(!\dash\engine\process::status())
		{
			\dash\header::set(400);
		}

		\content_api\v6::bye(self::$response);

	}

	private static function login()
	{
		$check_input = self::check_input();
		if(!$check_input)
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
			\dash\notif::error(T_("A verification code was sended to user before"));
			return false;
		}
	}


	private static function check_input()
	{
		$mobile = \dash\request::post('mobile');
		if(!$mobile)
		{
			\dash\notif::error(T_("Mobile not set"), 'mobile');
			return false;
		}

		$mobile = \dash\utility\filter::mobile($mobile);
		if(!$mobile)
		{
			\dash\notif::error(T_("Invalid mobile"), 'mobile');
			return false;
		}

		$verifycode = \dash\request::post('verifycode');
		if($verifycode)
		{
			if(!is_numeric($verifycode))
			{
				\dash\notif::error(T_("Invalid verifycode"), 'verifycode');
				return false;
			}

			$verifycode = intval($verifycode);

			if($verifycode < 10000 || $verifycode > 99999)
			{
				\dash\notif::error(T_("Verification code is out of range"), 'verifycode');
				return false;
			}
		}

		self::$mobile     = $mobile;
		self::$verifycode = $verifycode;

		return true;
	}


	private static function verify()
	{
		$check_input = self::check_input();
		if(!$check_input)
		{
			return false;
		}

		if(!self::$verifycode)
		{
			\dash\notif::error(T_("Verification code not set"), 'verifycode');
			return false;
		}

		$user_id = \dash\db\users::signup(['mobile' => self::$mobile]);
		if(!$user_id)
		{
			\dash\log::set('API-canNotSignupUserEnterVerify');
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
			\dash\notif::error(T_("No verifycation code sended to this phone number"));
			return false;
		}
		else
		{
			if(isset($check_log['status']) && in_array($check_log['status'], ['enable', 'notif', 'notifread']))
			{
				if(isset($check_log['datecreated']))
				{
					$old_time = strtotime($check_log['datecreated']);
					if((time() - $old_time) < self::$life_time)
					{
						if(isset($check_log['code']))
						{
							if(intval($check_log['code']) === intval(self::$verifycode))
							{
								\dash\db\logs::update(['status' => 'expire'], $check_log['id']);
								self::user_login_true();
								return true;
							}
							else
							{
								\dash\notif::error(T_("Invalid code"));
								return false;
							}
						}
						else
						{
							\dash\notif::error(T_("Verification code not set"));
							return false;
						}
					}
					else
					{
						\dash\notif::error(T_("Verification code was expired"));
						return false;
					}
				}
				else
				{
					\dash\notif::error(T_("Verification code not found"));
					return false;
				}
			}
			else
			{
				\dash\notif::error(T_("Verification code not found"));
				return false;
			}
		}
	}

	private static function user_login_true()
	{
		$result      = [];

		$new_user_id = self::$mobile_user_id;

		$old_user_id = \dash\user::id();

		$token       = \dash\header::get('token');

		$load_token  = \dash\db\user_auth::get(['auth' => $token, 'limit' => 1]);

		if(isset($load_token['gateway']) && isset($load_token['gateway_id']))
		{
			switch ($load_token['gateway'])
			{
				case 'android':
					\dash\db\user_android::update(['user_id' => $new_user_id, 'lastupdate' => date("Y-m-d H:i:s")], $load_token['gateway_id']);
					break;
			}
		}

		\dash\db\user_auth::update(['status' => 'expire'], $load_token['id']);

		$apikey = \dash\app\user_auth::make_user_auth($new_user_id, $load_token['gateway'], $load_token['gateway_id']);

		self::$response['apikey'] = $apikey;

		\dash\notif::ok(T_("Code is ok"));

		return true;
	}
}
?>