<?php
namespace lib;
/**
 * this lib handle content of our PHP framework, Dash
 * v 0.1
 */
class content
{
	// declare variables
	private static $content      = [];


	public static function initialize()
	{
		$list = @glob(root. '*');

		if(is_array($list))
		{
			foreach ($list as $key => $value)
			{
				if(@is_dir($value))
				{
					$explode = explode(DIRECTORY_SEPARATOR, $value);
					$content = end($explode);
					if(substr($content, 0, 7) === 'content')
					{
						array_push(self::$content, $content);
					}
				}
			}
		}
	}


	public static function list()
	{
		return self::$content;
	}


	public static function check($_content_name)
	{
		return in_array($_content_name, self::$content);
	}


	public static function is_content()
	{
		return self::check(...func_get_args());
	}
}
?>