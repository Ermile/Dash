<?php
namespace lib\engine;


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
