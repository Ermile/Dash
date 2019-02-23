<?php
namespace content_api\v6\enter;


trait verify
{

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

		$check_true = self::check_true_user();
		if(!$check_true)
		{
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
		$result               = [];
		$result['usertoken'] = self::$usertoken;

		if(intval(self::$user_id) === intval(self::$mobile_user_id))
		{
			$result['usercode'] = self::$usercode;
			$result['auth3'] = \dash\header::get('auth3');
		}
		else
		{
			\dash\db\user_android::update_where(['user_id' => self::$mobile_user_id], ['uniquecode' => self::$usertoken, 'user_id' => self::$user_id]);
			$result['usercode'] = \dash\coding::encode(self::$mobile_user_id);
			$user_auth          = \dash\app\user_auth::make_user_auth(self::$mobile_user_id, self::$x_app_request);
			$result['auth3']    = $user_auth;
		}

		\dash\notif::result($result);
		\dash\notif::ok(T_("Code ok"));
	}




}
?>