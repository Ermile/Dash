<?php
namespace dash;
/**
 * code if life or no
 */
class code
{
	// echo json of notif
	public static function compile()
	{
		if(\dash\request::json_accept() || \dash\request::ajax())
		{
			@header('Content-Type: application/json');
			echo \dash\notif::json();
		}
	}


	/**
	 * end the code and if needed echo json of notif
	 */
	public static function end()
	{
		self::compile();
		self::boom();
	}

	/**
	 * die code
	 */
	public static function bye($_string = null)
	{
		self::boom($_string);
	}


	/**
	 * exit code
	 */
	public static function boom($_string = null)
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
	public static function pretty($_data, $_pre = false)
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
