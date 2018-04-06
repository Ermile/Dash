<?php
namespace dash\engine;


class dev
{
	public static function debug()
	{
		if(\dash\option::config('debug'))
		{
			return true;
		}
		return false;
	}
}
?>
