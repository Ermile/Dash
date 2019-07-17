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
	public static function jsonBoom($_data = null, $_pretty = null, $_customHeader = null)
	{
		if(is_array($_data))
		{
			if($_pretty)
			{
				foreach ($_data as $key1 => $layer1)
				{
					if(is_string($layer1) && substr($layer1, 0, 1) === '{')
					{
						$_data[$key1] = json_decode($layer1, true);
						if(is_array($_data[$key1]))
						{
							foreach ($_data[$key1] as $key2 => $layer2)
							{
								if(is_string($layer2) && substr($layer2, 0, 1) === '{')
								{
									$_data[$key1][$key2] = json_decode($layer2, true);
								}
							}
						}
					}
				}
				$_data = json_encode($_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			}
			else
			{
				$_data = json_encode($_data, JSON_UNESCAPED_UNICODE);
			}
		}
		echo $_data;
		// set header
		if($_customHeader === 'manifest')
		{
			@header("Content-Type: application/manifest+json; charset=utf-8");
		}
		elseif($_customHeader === 'js')
		{
			@header("Content-Type: application/javascript; charset=utf-8");
		}
		elseif($_customHeader === 'html')
		{
			@header("Content-Type: text/html; charset=utf-8");
		}
		elseif($_customHeader === 'text')
		{
			@header("Content-Type: text/plain; charset=utf-8");
		}
		else
		{
			@header("Content-Type: application/json; charset=utf-8");
		}

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
