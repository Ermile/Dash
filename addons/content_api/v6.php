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


	public static function no($_code, $_msg = null, $_result = null)
	{
		\dash\header::set($_code);

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