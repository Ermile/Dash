<?php
namespace lib;

class date
{
	private static $lang = null;


	private static function format($_type, $_format = 'short')
	{
		$format     = "Y-m-d H:i:s";

		$short_time = \lib\option::config('short_time_format');
		$long_time  = \lib\option::config('long_time_format');

		$short_date = \lib\option::config('short_date_format');
		$long_date  = \lib\option::config('long_date_format');


		if($_type === 'time')
		{
			switch ($_format)
			{
				case 'long':
					$format = $long_time ? $long_time : "H:i:s";
					break;

				case 'short':
				default:
					$format = $short_time ? $short_time : "H:i";
					break;
			}
		}
		else
		{
			switch ($_format)
			{
				case 'long':
					$format = $long_date ? $long_date : "Y-m-d";
					break;

				case 'short':
				default:
					$format = $short_date ? $short_date : "Y/m/d";
					break;
			}
		}
		return $format;
	}


	private static function lang()
	{
		if(!self::$lang)
		{
			self::$lang = \lib\define::get_language();
		}

		return self::$lang;
	}


	public static function tdate($_timestamp = false, $_format = 'short')
	{
		if($_timestamp === false)
		{
			$_timestamp = time();
		}

		$_timestamp = intval($_timestamp);

		$lang = self::lang();

		if($lang === 'fa')
		{
			$result = \lib\utility\jdate::date(self::format('date', $_format),$_timestamp, false);
		}
		else
		{
			$result = date(self::format('date', $_format), $_timestamp);
		}

		return $result;
	}


	public static function ttime($_timestamp = false, $_format = 'short')
	{
		if($_timestamp === false)
		{
			$_timestamp = time();
		}
		$_timestamp = intval($_timestamp);

		$lang = self::lang();

		if($lang === 'fa')
		{
			$result = \lib\utility\jdate::date(self::format('time', $_format),$_timestamp, false);
		}
		else
		{
			$result = date(self::format('time', $_format), $_timestamp);
		}

		return $result;

	}
}
?>
