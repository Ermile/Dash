<?php
namespace lib\utility;

class nationalcode
{

	public static function check($_national_code)
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

		$fake =
		[
			1111111111,
			2222222222,
			3333333333,
			4444444444,
			5555555555,
			6666666666,
			7777777777,
			8888888888,
			9999999999,
		];

		if(in_array(intval($_national_code), $fake))
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
}
?>
