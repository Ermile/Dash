<?php
namespace dash\utility;

/** Filter of values : mobile ...etc **/
class filter
{
	public static function username($_username)
	{
		$_username = \dash\utility\convert::to_en_number($_username);

		$_username = preg_replace("/\_{2,}/", "_", $_username);
		$_username = preg_replace("/\-{2,}/", "-", $_username);

		if(mb_strlen($_username) < 5)
		{
			\dash\notif::error(T_("Slug must have at least 5 character"), 'username');
			return false;
		}

		if(mb_strlen($_username) > 50)
		{
			\dash\notif::error(T_("Please set the slug less than 50 character"), 'username');
			return false;
		}

		if(!preg_match("/^[A-Za-z0-9_\-]+$/", $_username))
		{
			\dash\notif::error(T_("Only [A-Za-z0-9_-] can use in slug"), 'username');
			return false;
		}

		if(!preg_match("/[A-Za-z]+/", $_username))
		{
			\dash\notif::error(T_("You must use a one character from [A-Za-z] in the slug"), 'username');
			return false;
		}

		if(is_numeric($_username))
		{
			\dash\notif::error(T_("Slug should contain a Latin letter"),'username');
			return false;
		}

		if(is_numeric(substr($_username, 0, 1)))
		{
			\dash\notif::error(T_("The slug must begin with latin letters"),'username');
			return false;
		}

		// if(!preg_match("/^[A-Za-z0-9]+$/", $_username))
		// {
		// 	\dash\notif::error(T_("Only [A-Za-z0-9] can use in slug"), 'username');
		// 	return false;
		// }

		// check slug
		if(mb_strlen($_username) >= 50)
		{
			\dash\notif::error(T_("Username must be less than 50 character"), 'username');
			return false;
		}

		$_username = mb_strtolower($_username);

		if(in_array($_username, []))
		{
			\dash\notif::error(T_("You can not choose this slug"), 'username');
			return false;
		}

		return $_username;
	}


	public static function is_larger($_number, $_max)
	{
		if(!is_numeric($_max))
		{
			return null;
		}

		if(!is_numeric($_number))
		{
			return null;
		}

		$len_number = strlen($_number);
		$len_max    = strlen($_max);

		if($len_number > $len_max)
		{
			return true;
		}
		elseif($len_number === $len_max)
		{
			for ($i = 0; $i < $len_number; $i++)
			{
				$n = substr($_number, $i, 1);
				$m = substr($_max, $i, 1);

				if(intval($n) > intval($m))
				{
					return true;
				}
			}

			return false;
		}
		elseif($len_number < $len_max)
		{
			return false;
		}
	}



	public static function email($_email)
	{
		return filter_var($_email, FILTER_VALIDATE_EMAIL);
	}


	public static function url($_url)
	{
		return filter_var($_url, FILTER_VALIDATE_URL);
	}


	public static function nationalcode($_national_code)
	{
		$check = false;

		if(!is_numeric($_national_code) || !$_national_code)
		{
			return false;
		}

		if(mb_strlen($_national_code) <> 10)
		{
			return false;
		}

		if($_national_code != round($_national_code, 0))
		{
			return false;
		}


		$split      = str_split($_national_code);
		$main_place = [];
		$i          = 10;
		$p          = 0;

		foreach ($split as $n => $value)
		{
			$main_place[$i] = $value;

			if ($i != 1)
			{
				$p = $p + ($value * $i);
			}
			$i--;
		}

		$b = fmod($p, 11);

		if ($b < 2)
		{
			if (intval($main_place[1]) === intval($b))
			{
				$check = true;
			}
			else
			{
				$check = false;
			}
		}
		else
		{
			if (intval($main_place[1]) === intval(11 - $b))
			{
				$check = true;
			}
			else
			{
				$check = false;
			}
		}


		return $check;
	}


	/**
	 * this function filter mobile value
	 * 1. remove space from mobile number if exist
	 * 2. remove + from first of number if exist
	 * 3. remove 0 from iranian number
	 * 4. start 98 to start of number for iranian number
	 *
	 * @param  [str] $_mobile
	 * @return [str] filtered mobile number
	 */
	public static function mobile($_mobile)
	{
		$mymobile = str_replace(' ', '', $_mobile);

		// if user enter plus in start of number delete it
		if(substr($mymobile, 0, 1) === '+')
		{
			$mymobile = substr($mymobile, 1);
		}
		elseif(substr($mymobile, 0, 2) === '00')
		{
			// if user enter 00 in start of number delete it
			$mymobile = substr($mymobile, 2);
		}
		elseif(substr($mymobile, 0, 1) === '0')
		{
			$mymobile = substr($mymobile, 1);
		}

		if(!ctype_digit($mymobile))
		{
			return false;
		}

		if(intval($mymobile) < 0)
		{
			return false;
		}

		// check max and min number
		if(mb_strlen($mymobile) > 15 || mb_strlen($mymobile) < 8)
		{
			return false;
		}

		// if start with zero then remove it
		elseif(substr($mymobile, 0, 1) === '0')
		{
			$mymobile = substr($mymobile, 1);
		}

		// if user type 10 number like 935 726 9759 and number start with 9 append 98 at first
		// Juest support Iranain mobile

		if(mb_strlen($mymobile) === 10 && substr($mymobile, 0, 1) === '9')
		{
			$mymobile = '98'.$mymobile;
		}

		return $mymobile;
	}


	public static function ir_mobile($_number)
	{
		$_number = self::mobile($_number);
		if(!$_number)
		{
			return $_number;
		}

		if(mb_strlen($_number) !== 12)
		{
			return false;
		}

		if(substr($_number, 0, 3) !== '989')
		{
			return false;
		}

		return $_number;
	}


	/**
	 * filter birthdate value
	 * @param  [str] $_date raw date for filtering in function
	 * @param  [str] $_arg  type of input like year or month on day
	 * @return [str]        filtered birthdate
	 */
	public static function birthdate($_date, $_arg)
	{
		if($_arg && method_exists(__CLASS__, $_arg) )
		{
			$mydate = self::$_arg($_date);
		}
		else
		{
			$mydate	= $_date;
		}

		return $mydate;
	}

	/**
	 * change simple string in any language to english
	 * @param  [type] $_string  raw string
	 * @param  [type] $_splitor if needed pass splitor
	 * @return [type]           return the new slug in english
	 */
	public static function slug($_string, $_splitor = null, $_rules = true)
	{

		if($_rules === true)
		{
			$slugify = new \dash\utility\slugify();
			$slugify->activateRuleset('persian');
		}
		elseif($_rules === 'persian')
		{
			$_string = mb_strtolower($_string);
			$_string = mb_ereg_replace('([^ءئآا-ی۰-۹a-z0-9]|-)+', '-', $_string);
			$_string = trim($_string);
			$_string = mb_strtolower($_string);

			return $_string;
		}
		else
		{
			$slugify = new \dash\utility\slugify();
		}

		if($_splitor)
		{
			return $slugify->slugify($_string, $_splitor);
		}
		else
		{
			return $slugify->slugify($_string);
		}
	}


	/**
	 * decode every fileld need to decode in array
	 *
	 * @param      <type>  $_array    The array
	 * @param      <type>  $_field    The field
	 * @param      array   $_options  The options
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function meta_decode($_array, $_field = null, $_options = [])
	{
		$field = $_field ? $_field : "/^(.+_meta|meta)$/";
		if(!is_array($_array))
		{
			return $_array;
		}
		array_walk($_array, function(&$_row, $_key, $_options)
		{
			$keys = array_keys($_row);
			$json_fields = preg_grep($_options[0], $keys);
			$to_array = true;
			$options = $_options[1];
			if(array_key_exists('return_object', $options) && $options['return_object'] === true)
			{
				$to_array = false;
			}
			foreach ($json_fields as $key => $value) {
				$row_value = $_row[$value];
				// $row_value = preg_replace("/\\\/", '\\\\\\\\', $row_value);
				// $row_value = preg_replace("#\n#Ui", "\\n", $row_value);
				$json = json_decode($row_value, $to_array);
				$_row[$value] = is_null($json) ? $_row[$value] : $json;
			}
		}, [$field, $_options]);
		return $_array;
	}
}
?>