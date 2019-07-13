<?php
namespace dash\utility;


class catch_file
{
	private static $catch_folder = 'public_html/files/catch_file/';


	public static function get($_name, $_is_array = true)
	{
		$get = self::catch_file($_name);
		if($get && $_is_array)
		{
			$get = json_decode($get, true);
		}

		$file_time = self::file_time();

		if(isset($file_time[$_name]['life_time']) && isset($file_time[$_name]['last_update']))
		{
			if(time() - intval($file_time[$_name]['last_update']) > intval($file_time[$_name]['life_time']))
			{
				return null;
			}
		}

		return $get;
	}


	public static function set($_name, $_data, $_update_time = null)
	{
		if(is_array($_data) || is_object($_data))
		{
			$_data = json_encode($_data);
		}

		self::catch_file($_name, $_data);

		if($_update_time)
		{
			$file_time = self::file_time();

			$file_time[$_name] =
			[
				'last_update'      => time(),
				'last_update_date' => date("Y-m-d H:i:s"),
				'life_time'        => $_update_time
			];

			self::file_time($file_time);
		}
	}


	public static function clean($_name)
	{
		$file_addr = root. self::$catch_folder. $_name;
		if(is_file($file_addr))
		{
			\dash\file::delete($file_addr);

			$file_time = self::file_time();
			if(isset($file_time[$_name]))
			{
				unset($file_time[$_name]);

				self::file_time($file_time);
			}
		}
	}


	private static function file_time($_data = null)
	{
		$file_addr = root. self::$catch_folder. 'updatetimefile.me.json';
		$file_time = \dash\file::read($file_addr);
		$file_time = json_decode($file_time, true);

		if(!is_array($file_time))
		{
			$file_time = [];
		}

		if($_data !== null)
		{
			\dash\file::write($file_addr, json_encode($_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		}

		return $file_time;
	}


	private static function catch_file($_name, $_data = null)
	{
		$file_addr = root. self::$catch_folder;

		if(!is_dir($file_addr))
		{
			\dash\file::makeDir($file_addr, null, true);
		}

		$file_addr .= $_name;

		if($_data)
		{
			\dash\file::write($file_addr, $_data);
			return true;
		}
		else
		{
			if(!is_file($file_addr))
			{
				return null;
			}
			else
			{
				return \dash\file::read($file_addr);
			}
		}
	}
}
?>