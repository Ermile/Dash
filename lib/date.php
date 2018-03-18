<?php
namespace lib;

class date
{
	private static $lang = null;


	/**
     * get month precent
     *
     *
     * @param      <type>  $_type  The type
     */
    public static function month_precent($_type = null)
    {
    	$lang = \lib\language::current();

    	if($lang === 'fa')
    	{
			$d = intval(\lib\utility\jdate::date("d", false, false));
			$m = intval(\lib\utility\jdate::date("m", false, false));
			$t = intval(\lib\utility\jdate::date("t", false, false));
    	}
    	else
    	{
			$d = intval(date("d"));
			$m = intval(date("m"));
			$t = intval(date("t"));
    	}

        $left   = round(($d * 100) / $t);
        $remain = round((($t - $d) * 100) / $t);

        $return = null;
        switch ($_type)
        {
            case 'left':
                $return = $left;
                break;
            case 'remain':
                $return = $remain;
                break;
            default:
                $return =
                [
					'left'   => $left,
					'remain' => $remain,
					'count'  => $t,
                ];
                break;
        }
        return $return;
    }


    /**
     * check language and if needed convert to persian date
     * else show default date
     * @param  [type] $_date [description]
     * @return [type]        [description]
     */
    public static function fit_lang($_format, $_stamp = false, $_type = false, $_persianChar = true)
    {
    	$result = null;

    	if(mb_strlen($_stamp) < 2)
    	{
    		$_stamp = false;
    	}

        // get target language
    	if($_type === 'default')
    	{
    		$_type = \lib\language::default();
    	}
    	elseif($_type === 'current')
    	{
    		$_type = \lib\language::current();
    	}

        // if need persian use it else use default date function
    	if($_type === true || $_type === 'fa' || $_type === 'fa_IR')
    	{
    		$result = \lib\utility\jdate::date($_format, $_stamp, $_persianChar);
    	}
    	else
    	{
    		if($_stamp)
    		{
    			$result = date($_format, $_stamp);
    		}
    		else
    		{
    			$result = date($_format);
    		}
    	}

    	return $result;
    }


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
			self::$lang = \lib\language::current();
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
