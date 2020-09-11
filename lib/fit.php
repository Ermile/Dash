<?php
namespace dash;


class fit{


	/**
	 * Converts all numbers to persian number
	 * @param  [type] $_txt input text or numbers
	 * @return [type]       [description]
	 */
	public static function number($_txt, $_autoFormat = true, $_lang = null)
	{
		$new_text = $_txt;

		// auto format
		if($_autoFormat)
		{
			if(is_numeric($new_text))
			{
				$new_text = number_format($new_text);
			}
		}

		$persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
		$arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
		$english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
		// if language is not set use default language
		if(!$_lang)
		{
			$_lang = \dash\language::current();
		}

		switch ($_lang)
		{
			case 'persian':
			case 'farsi':
			case 'fa':
				// convert english and arabic numbers to persian number
				$new_text = str_replace($english, $persian, $new_text);
				$new_text = str_replace($arabic, $persian, $new_text);
				break;

			case 'arabic':
			case 'ar':
				// convert english and arabic numbers to persian number
				$new_text = str_replace($persian, $arabic, $new_text);
				$new_text = str_replace($english, $arabic, $new_text);
				break;

			case 'english':
			case 'en':
			default:
				// convert english and arabic numbers to persian number
				$new_text = str_replace($persian, $english, $new_text);
				$new_text = str_replace($arabic, $english, $new_text);
				break;
		}
		// return result in selected language
		return $new_text;
	}


	public static function price($_price)
	{
		return self::number_decimal($_price, 'en');
	}


	public static function price_old($_price, $_forceEn = false)
	{
		if($_forceEn)
		{
			return self::number($_price, true, 'en');
		}
		return self::number($_price);
	}


	public static function number_decimal($_number, $_lang = null)
	{
		if(!is_numeric($_number))
		{
			return $_number;
		}


		$new_number = (string) $_number;

		if(strpos($new_number, '.') !== false)
		{
			$number  = substr($new_number, 0, strpos($new_number, '.'));
			$decimal = substr($new_number, strpos($new_number, '.') + 1 );
			$fit = self::number($number, true, $_lang);

			if(preg_match("/[1-9]/", $decimal))
			{
				$decimal = preg_replace("/0+$/", '', $decimal);
				$fit .= '.'. self::text($decimal, $_lang);
			}
			return $fit;
		}
		else
		{
			return self::number($_number, true, $_lang);
		}

	}

	public static function stats($_txt)
	{
		$number = $_txt;

		if(!is_numeric($number))
		{
			return '-';
		}

		$result = null;

		$number = floatval($number);

		if($number < 100000)
		{
			$result = self::number($number);
		}
		elseif($number < 1000000)
		{
			$k = round(($number / 1000), 2);
			$result = self::text($k). ' K';
		}
		elseif($number < 1000000000)
		{
			$k = round($number / 1000000, 2);
			$result = self::text($k). ' M';
		}
		else
		{
			$k = round($number / 1000000000, 2);
			$result = self::text($k). ' B';
		}

		return $result;

	}


	public static function text($_txt, $_lang = null)
	{
		return self::number($_txt, false, $_lang);
	}


	public static function date($_date)
	{
		if(!$_date)
		{
			return null;
		}
		return \dash\datetime::fit($_date, null, 'date');
	}


	public static function time($_date)
	{
		if(!$_date)
		{
			return null;
		}
		return \dash\datetime::fit($_date, null, 'time');
	}

	public static function date_time($_date)
	{
		if(!$_date)
		{
			return null;
		}
		return \dash\datetime::fit($_date);
	}

	public static function date_human($_date)
	{
		if(!$_date)
		{
			return null;
		}
		return \dash\datetime::fit($_date, 'human', 'year');
	}


	public static function mobile($_number)
	{
		$new_text = $_number;
		if(is_numeric($new_text))
		{
			if(strlen($new_text) == 12)
			{
				$country     = substr($new_text, 0, 2);
				$firstChain  = substr($new_text, 2, 3);
				$secondChain = substr($new_text, 5, 3);
				$thirdChain  = substr($new_text, 8, 4);
				$new_text    = ''. $country . '-'. $firstChain . '-' . $secondChain . '-' . $thirdChain;
			}
		}

		$new_text = self::text($new_text);
		return $new_text;
	}


	public static function tel($_number)
	{
		$new_text = $_number;
		if(is_numeric($new_text))
		{
			if(strlen($new_text) == 12)
			{
				$country     = substr($new_text, 0, 2);
				$firstChain  = substr($new_text, 2, 3);
				$secondChain = substr($new_text, 5, 3);
				$thirdChain  = substr($new_text, 8, 4);
				$new_text    = $firstChain . '-' . $secondChain . '-' . $thirdChain;
			}
			elseif(strlen($new_text) == 10)
			{
				$firstChain  = substr($new_text, 0, 3);
				$secondChain = substr($new_text, 3, 3);
				$thirdChain  = substr($new_text, 6, 4);
				$new_text     = $firstChain . '-' . $secondChain . '-' . $thirdChain;
			}
		}

		$new_text = self::number($new_text);
		return $new_text;
	}



	public static function file_size($_size)
	{
		return \dash\upload\size::readableSize($_size);
	}
}
?>