<?php
namespace lib\engine;

class proccess
{

	private static $status = true;


	public static function continue()
	{
		self::$status = true;
	}


	public static function stop()
	{
		self::$status = false;
	}


	public static function status()
	{
		return self::$status;
	}
}
?>
