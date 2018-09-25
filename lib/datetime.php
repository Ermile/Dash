<?php
namespace dash;

class datetime
{

	/**
	 * return all format supported
	 * @param  [type]  $_type [description]
	 * @param  boolean $_long [description]
	 * @return [type]         [description]
	 */
	public static function format($_type = null, $_long = null, $_lang = null)
	{
		switch ($_type)
		{
			case 'date':
				switch ($_long)
				{
					case true:
						// Tuesday 25 September 2018
						return 'l d F Y';
						break;

					case false:
					default:
						// 2018-09-25
						return 'Y-m-d';
						break;
				}
				break;

			case 'time':
				switch ($_long)
				{
					case true:
						// 4:05:52 PM
						return 'g:i:s A';
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
				switch ($_long)
				{
					case 'shortTime':
						return 'l d F Y'. ' '. 'H:i';
						break;

					case 'shortDate':
						return 'Y-m-d'. ' '. 'H:i:s';
						break;

					case true:
						return 'l d F Y'. ' '. 'H:i:s';
						break;

					case false:
					default:
						return 'Y-m-d'. ' '. 'H:i';
						break;
				}
				break;
		}
	}


	public static function get(
		$_datetime,
		$_long = null,
		$_type = 'datetime',
		$_lang = null,
		$_convertNumber = null
	)
	{
		// step1 - check datetime

		// step2 - get new format
		$myFormat   = self::format($_type, $_long);
		$myDatetime = strtotime($_datetime);
		$finalDate  = null;
		// detect current lang if not set
		if($_lang === null)
		{
			$_lang = \dash\language::current();
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


	public static function fit($_datetime, $_long = null)
	{
		return self::get($_datetime, $_long);
	}

}
?>