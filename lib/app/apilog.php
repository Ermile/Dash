<?php
namespace dash\app;

class apilog
{
	private static $apilog = [];

	public static function start()
	{
		self::$apilog['user_id']        = null;
		self::$apilog['token']          = null; // 100
		self::$apilog['apikey']         = null; // 100
		self::$apilog['appkey']         = null; // 100
		self::$apilog['zoneid']         = null; // 100
		self::$apilog['url']            = substr(\dash\url::pwd(), 0, 2000);
		self::$apilog['method']         = substr(\dash\request::is(), 0, 200);
		self::$apilog['header']         = $headerjson = json_encode(getallheaders());
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

		self::$apilog['user_id']        = null;
		self::$apilog['token']          = null; // 100
		self::$apilog['apikey']         = null; // 100
		self::$apilog['appkey']         = null; // 100
		self::$apilog['zoneid']         = null; // 100
		self::$apilog['pagestatus']     = null; // 100
		self::$apilog['resultstatus']   = \dash\engine\process::status() ? 'true' : 'false'; // 100
		self::$apilog['responseheader'] = null;
		self::$apilog['responsebody']   = $_result;
		self::$apilog['dateresponse']   = date("Y-m-d H:i:s");

		self::save_db();
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