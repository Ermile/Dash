<?php
namespace dash\engine;


class dev
{
	public static function debug()
	{
		if(\lib\option::config('debug'))
		{
			return true;
		}
		return false;
	}
}
?>
