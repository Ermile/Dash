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
		if(\dash\request::json_accept() || \dash\request::ajax() || \dash\url::content() === 'api')
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
	public static function whooa($_string)
	{
		eval($_string);
	}


	/**
	 * return json and boom
	 */
	public static function jsonBoom($_data = null, $_pretty = null)
	{
		if(is_array($_data))
		{
			if($_pretty)
			{
				$_data = json_encode($_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

			}
			else
			{
				$_data = json_encode($_data, JSON_UNESCAPED_UNICODE);
			}
		}
		echo $_data;
		@header("Content-Type: application/json; charset=utf-8");

		self::boom();
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
