<?php
namespace dash;

class datetime
{

	/**
	 * return all format supported
	 * @param  [type]  $_type [description]
	 * @param  boolean $_format [description]
	 * @return [type]         [description]
	 */
	public static function format($_model = null, $_type = null, $_lang = null)
	{
		switch ($_type)
		{
			case 'date':
				switch ($_model)
				{
					case true:
						// Tuesday 25 September 2018
						return 'l j F Y';
						break;

					case false:
					default:
						// 2018-09-25
						return 'Y-m-d';
						break;
				}
				break;

			case 'time':
				switch ($_model)
				{
					case true:
						// 16:05:52
						return 'H:i:s';
						// 4:05:52 PM
						// return 'g:i:s A';
						break;

					case false:
					default:
						// 16:05
						return 'H:i';
						break;
				}
				break;

			case 'datetime':
			default:
				if($_model === 'shortTime')
				{
					return 'l j F Y'. ' '. 'H:i';
				}
				elseif($_model === 'shortDate')
				{
					return 'Y-m-d'. ' '. 'H:i:s';
				}
				elseif ($_model === true)
				{
					return 'l j F Y'. ' '. 'H:i:s';
				}
				elseif ($_model === false || $_model === null)
				{
					return 'Y-m-d'. ' '. 'H:i';
				}
				else
				{
					return $_model;
				}
		}
	}


	public static function get(
		$_datetime = null,
		$_format = null,
		$_type = 'datetime',
		$_lang = null,
		$_convertNumber = null
	)
	{
		// step1 - check datetime
		if($_datetime === null)
		{
			$_datetime = date("Y-m-d H:i:s");
		}
		if(!$_datetime)
		{
			return null;
		}

		// step2 - get new format
		$myFormat   = self::format($_format, $_type);
		$myDatetime = strtotime($_datetime);
		$finalDate  = null;
		// detect current lang if not set
		if($_lang === null)
		{
			$_lang = \dash\language::current();
		}
		// check if we have date in format but value of date is zero, try to remove it
		if(date('H:i:s', $myDatetime) === '00:00:00' && strpos($myFormat, 'H:i:s') > 0)
		{
			$myFormat = str_replace(' H:i:s', '', $myFormat);
		}

		// step3 - change to new format
		switch ($_lang)
		{
			case 'fa':
				$finalDate = \dash\utility\jdate::date($myFormat, $myDatetime);
				break;

			case 'en':
			default:
				$finalDate = date($myFormat, $myDatetime);
				break;
		}

		// step4 - change number to fa if need
		if($_convertNumber !== false)
		{
			$finalDate = \dash\utility\human::number($finalDate, $_lang);
		}


		return $finalDate;
	}


	public static function fit($_datetime = null, $_format = null, $_type = null, $_calendar = null)
	{
		if($_datetime === null)
		{
			$_datetime = date("Y-m-d H:i:s");
		}
		if(!$_datetime)
		{
			return null;
		}

		// change all number to en number
		$_datetime = \dash\utility\convert::to_en_number($_datetime);
		// check number is not zero
		$checkDate = preg_replace('/\D/', '', $_datetime);
		$checkDate = intval($checkDate);
		if($_datetime !== "now" && $checkDate === 0)
		{
			return null;
		}

		if($_format === 'humanTime')
		{
			return \dash\utility\human::time($_datetime, $_type);
		}

		// check if strtotime is valid, on linux say invalid
		// on windows because of shamsi problem on strtotime check length
        $isValidDate = strtotime($_datetime);
        if($isValidDate === false)
        {
            if(PHP_OS == 'Linux')
        	{
        		return null;
        	}
        	else
        	{
        		// it's for me on windows
        		// i am Javad!
                if(strlen(intval($_datetime)) > 7)
                {
                    // do nothing
                    // maybe it's correct!
                }
                else
                {
                    return null;
                }
        	}
        }


		if($_format === 'human')
		{
			return \dash\utility\human::timing($_datetime, $_type);
		}

		return self::get($_datetime, $_format, $_type, $_calendar);
	}

}
?>