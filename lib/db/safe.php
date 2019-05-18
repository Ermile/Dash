<?php
namespace dash\db;


class safe
{
	public static function value($_value)
	{
		if(\dash\db::$link)
		{
			$_value = \mysqli_real_escape_string(\dash\db::$link, $_value);
		}
		return $_value;
	}
}
?>
