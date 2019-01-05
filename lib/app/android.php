<?php
namespace dash\app;

class android
{
	public static function addr()
	{
		return root. '/android.me.json';
	}


	public static function load($_save = null)
	{
		$addr = self::addr();
		$get  = [];

		if($_save)
		{
			if(is_array($_save))
			{
				$_save = json_encode($_save, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			}

			\dash\file::write($addr, $_save);
		}
		else
		{
			if(is_file($addr))
			{
				$get = \dash\file::read($addr);
				$get = json_decode($get, true);
			}

			if(!is_array($get))
			{
				$get = [];
			}
		}

		return $get;
	}


	public static function token($_set_token = false)
	{
		if($_set_token)
		{
			return self::set_token();
		}

		$detail = self::load();
		if(isset($detail['token']))
		{
			return $detail['token'];
		}
		return null;
	}



	private static function set_token()
	{
		$token = 'Ermile';
		$token .= '_DASH_';
		$token .= '_Android_';
		$token .= time();
		$token .= md5(rand(1,9999));
		$token = \dash\utility::hasher($token);

		$load = self::load();
		$load['token'] = $token;
		self::load($load);
	}
}
?>