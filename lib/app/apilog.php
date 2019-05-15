<?php
namespace dash\app;

class apilog
{
	private static $apilog     = [];
	private static $static_var = [];

	public static function start()
	{
		self::$apilog['user_id']        = null;
		self::$apilog['token']          = null; // 100
		self::$apilog['apikey']         = null; // 100
		self::$apilog['appkey']         = null; // 100
		self::$apilog['zoneid']         = null; // 100
		self::$apilog['url']            = substr(\dash\url::pwd(), 0, 2000);
		self::$apilog['method']         = substr(\dash\request::is(), 0, 200);
		self::$apilog['header']         = $headerjson = json_encode(\dash\header::get());
		self::$apilog['headerlen']      = mb_strlen($headerjson);
		self::$apilog['body']           = $body = json_encode(\dash\request::post());
		self::$apilog['bodylen']        = mb_strlen($body);
		self::$apilog['datesend']       = date("Y-m-d H:i:s");

		self::$apilog['pagestatus']     = null; // 100
		self::$apilog['resultstatus']   = null; // 100
		self::$apilog['responseheader'] = null;
		self::$apilog['responsebody']   = null;
		self::$apilog['dateresponse']   = null;
	}


	public static function save($_result = null)
	{
		if(is_array($_result) || is_object($_result))
		{
			$_result = json_encode($_result);
		}

		self::$apilog['user_id']        = \dash\user::id();
		self::$apilog['token']          = self::static_var('token'); // 100
		self::$apilog['apikey']         = self::static_var('apikey'); // 100
		self::$apilog['appkey']         = self::static_var('appkey'); // 100
		self::$apilog['zoneid']         = self::static_var('zoneid'); // 100
		self::$apilog['pagestatus']     = null; // 100
		self::$apilog['resultstatus']   = \dash\engine\process::status() ? 'true' : 'false'; // 100
		self::$apilog['responseheader'] = null;
		self::$apilog['responsebody']   = $_result;
		self::$apilog['dateresponse']   = date("Y-m-d H:i:s");

		self::save_db();
	}


	public static function static_var($_name, $_value = null)
	{
		if($_value !== null)
		{
			self::$static_var[$_name] = $_value;
			return;
		}

		if(array_key_exists($_name, self::$static_var))
		{
			return self::$static_var[$_name];
		}
		return null;
	}


	private static function save_db()
	{
		if(self::$apilog)
		{
			\dash\db\apilog::insert(self::$apilog);
		}
	}
}

?>