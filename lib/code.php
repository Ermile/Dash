<?php
namespace lib;
/**
 * code if life or no
 */
class code
{
	/**
	 * exit the code
	 */
	public static function end()
	{
		self::exit();
	}

	/**
	 * die code
	 */
	public static function die($_string = null)
	{
		self::exit($_string);
	}


	/**
	 * exit code
	 */
	public static function exit($_string = null)
	{
		exit($_string);
	}


	/**
	 * var_dump data
	 *
	 * @param      <type>  $_data  The data
	 */
	public static function dump($_data, $_pre = false)
	{
		if($_pre)
		{
			echo '<pre>';
		}

		var_dump($_data);

		if($_pre)
		{
			echo '</pre>';
		}
	}


	/**
	 * print_r data
	 */
	public static function print($_data, $_pre = false)
	{
		if($_pre)
		{
			echo '<pre>';
		}

		print_r($_data);

		if($_pre)
		{
			echo '</pre>';
		}
	}


	/**
	 * eval code
	 */
	public static function eval($_string)
	{
		eval($_string);
	}



	/**
	 * sleep code
	 *
	 * @param      <type>  $_seconds  The seconds
	 */
	public static function sleep($_seconds)
	{
		sleep($_seconds);
	}
}
?>

