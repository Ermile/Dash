<?php
namespace content_api;


class v6
{
	public static $v6 = [];

	public static function check_appkey()
	{
		$appkey = \dash\header::get('appkey');

		if(!trim($appkey))
		{
			self::no(400, T_("Appkey not set"));
		}

		$all_app_key = \dash\option::config('api_v6', 'appkey');
		if($all_app_key && is_array($all_app_key))
		{
			if(in_array($appkey, $all_app_key))
			{
				return true;
			}
			else
			{
				self::no(400, T_("Invalid app key"));
			}
		}
		else
		{
			self::no(400, T_("App key not installed"));
		}
	}

	public static function check_token()
	{
		$token = \dash\header::get('token');

		if(!$token || mb_strlen($token) !== 32)
		{
			self::no(401, T_("Invalid token"));
		}

		$get =
		[
			'status'  => 'enable',
			'user_id' => null,
			'type'    => 'guest',
			'auth'    => $token,
			'limit'   => 1,
		];

		$get = \dash\db\user_auth::get($get);

		if(!isset($get['id']) || !isset($get['datecreated']))
		{
			self::no(401, T_("Invalid token"));
		}

		$time_left = time() - strtotime($get['datecreated']);

		$life_time = 60 * 3;

		if($time_left > $life_time)
		{
			\dash\db\user_auth::update(['status' => 'expire'], $get['id']);
			self::no(401, T_("Token is expire"));
		}

		\dash\db\user_auth::update(['status' => 'used'], $get['id']);

		return true;
	}


	public static function no($_code, $_msg = null, $_result = null)
	{
		\dash\header::set($_code);

		if(in_array(intval($_code), [400,401,403,404,429,405,415]) && \dash\engine\process::status())
		{
			\dash\engine\process::stop();
		}

		if($_msg)
		{
			\dash\notif::error($_msg);
		}
		self::bye($_result);
	}


	public static function bye($_result = null)
	{
		\dash\notif::api($_result);
	}


}
?>