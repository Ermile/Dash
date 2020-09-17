<?php
namespace dash;


class number
{
	public static function clean($_number)
	{

		if(is_int($_number) || is_float($_number) || is_numeric($_number))
		{
			return $_number;
		}

		if(is_string($_number))
		{
			$_number = \dash\utility\convert::to_en_number($_number);
			$replace = ['{', '}', '(', ')', '_', '-', '+',' ', ','];
			$_number = str_replace($replace, '', $_number);
		}

		return $_number;
	}


	public static function is($_check)
	{
		$_check = self::clean($_check);

		if(!is_numeric($_check))
		{
			return false;
		}

		// infinity number
		if(is_infinite($_check))
		{
			return false;
		}

		return true;
	}


	public static function is_larger($_number, $_max)
	{
		if(!self::is($_max))
		{
			return null;
		}

		if(!self::is($_number))
		{
			return null;
		}

		$len_number = strlen(self::to_number($_number));
		$len_max    = strlen(self::to_number($_max));

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


	/**
	 * Convert 1E+3 to 1000
	 * Only call this function in this file to check is larger
	 *
	 * Only number > 0 can convert to string
	 *
	 * @param      <type>  $_number  The number
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function to_number($_number)
	{
		if(preg_match("/^(\d+)(\.(\d+))?(e|E)\+(\d+)$/", $_number, $split))
		{
			$string_number = $split[1];
			$string_number .= $split[3];

			$repeat = intval($split[5]) - strlen($string_number);

			if($repeat >= 0)
			{
				$string_number .= str_repeat('0', $repeat);
			}
			return $string_number;
		}

		return $_number;
	}

}
?>