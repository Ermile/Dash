<?php
namespace lib;

class date
{
	private static $lang = null;


	private static function formatFinder($_type, $_format = 'short')
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
			$result = \lib\utility\jdate::date(self::formatFinder('date', $_format),$_timestamp, false);
		}
		else
		{
			$result = date(self::formatFinder('date', $_format), $_timestamp);
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
			$result = \lib\utility\jdate::date(self::formatFinder('time', $_format),$_timestamp, false);
		}
		else
		{
			$result = date(self::formatFinder('time', $_format), $_timestamp);
		}

		return $result;

	}

	public static function force()
	{

	}


	public static function forceEn()
	{

	}


	public static function forceFa($_date)
	{

	}



	public static function db($_date, $_seperator = '-')
	{
		$myDate = trim($_date);
		if(!$myDate)
		{
			return null;
		}
		$myDate    = \lib\utility\convert::to_en_number($myDate);
		$myDate    = str_replace('/', '-', $myDate);
		$myDateLen = strlen($myDate);

		if($myDateLen === 10)
		{
			// do nothing
		}
		elseif($myDateLen === 8 && is_numeric($myDateLen) && strpos($myDateLen, '-') === false)
		{
			// try to fix more on date as yyyy-mm-dd soon
			$convertedDate = strtotime($myDate);
			if ($convertedDate === FALSE)
			{
				return false;
			}
			$myDate = date('Y-m-d', $convertedDate);
		}
		else
		{
			return false;
		}

		if($_seperator !== '-')
		{

			$convertedDate = \DateTime::createFromFormat("Y-m-d", $myDate);
			$myDate = $convertedDate->format('Y'. $_seperator. 'm'. $_seperator. 'd');
		}

		// retult always have 10 chars with format yyyy-mm-dd
		return $myDate;
	}


	public static function format($_date, $_format = 'Y-m-d', $_formatInput = 'Y-m-d')
	{
		$myDate = self::db($_date);

		$convertedDate = strtotime($myDate);
		if ($convertedDate === FALSE)
		{
			$convertedDate = \DateTime::createFromFormat($_formatInput, $myDate);
			if($convertedDate)
			{
				$convertedDate = $convertedDate->format($_format);
			}
		}
		else
		{
			$convertedDate = date($_format, $convertedDate);
		}

		return $convertedDate;
	}


}
?>
