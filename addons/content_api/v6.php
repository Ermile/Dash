<?php
namespace content_api;


class v6
{
	public static $v6 = [];

	public static function check_appkey()
	{
		$appkey = \dash\header::get('appkey');

		\dash\app\apilog::static_var('appkey', $appkey);

		if(!trim($appkey))
		{
			self::no(400, T_("Appkey not set"));
		}

		$appkey_is_ok              = \dash\app\user_auth::check_appkey($appkey);

		self::$v6['appkey_detail'] = $appkey_is_ok;

		if($appkey_is_ok)
		{
			return true;
		}
		else
		{
			self::no(400, T_("Invalid app key"));
		}

	}

	public static function check_token()
	{
		$token = \dash\header::get('token');

		\dash\app\apilog::static_var('token', $token);

		if(!$token)
		{
			self::no(401, T_("token not set"));
		}

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


	public static function check_apikey()
	{
		$apikey = \dash\header::get('apikey');

		\dash\app\apilog::static_var('apikey', $apikey);

		if(!$apikey)
		{
			self::no(401, T_("apikey not set"));
		}

		if(mb_strlen($apikey) !== 32)
		{
			self::no(401, T_("Invalid apikey"));
		}

		$get =
		[
			'status'  => 'enable',
			'user_id' => [" IS ", " NOT NULL "],
			'type'    => 'member',
			'auth'    => $apikey,
			'limit'   => 1,
		];

		$get = \dash\db\user_auth::get($get);

		if(!isset($get['id']) || !isset($get['datecreated']) || !isset($get['user_id']))
		{
			self::no(401, T_("Invalid apikey"));
		}

		self::api_user_login($get['id']);

		if(!\dash\user::id())
		{
			\dash\user::init($get['user_id']);
		}

		return true;
	}


	private static function api_user_login($_id)
	{
		$session_id = \dash\url::root(). 'API'. $_id;

		// if a session is currently opened, close it
		if(session_id() != '')
		{
			session_write_close();
		}

		// use new id
		session_id($session_id);
		// start new session
		session_start();
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
		\dash\app\apilog::save($_result);
		\dash\notif::api($_result);
	}


}
?>