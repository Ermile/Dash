<?php
namespace lib;
/**
 * Class for notif.
 */
class notif
{
	private static $notif = [];


	private static function make($_type, $_text, $_meta)
	{
		if(!isset(self::$notif['msg']))
		{
			self::$notif['msg'] = [];
		}

		$make =
		[
			'type' => $_type,
			'text' => $_text,
			'meta' => $_meta,
		];

		array_push(self::$notif['msg'], $make);
	}


	public static function info($_text, $_meta = [])
	{
		self::make('info', $_text, $_meta);
	}


	public static function ok($_text, $_meta = [])
	{
		self::make('ok', $_text, $_meta);
	}


	public static function warn($_text, $_meta = [])
	{
		self::make('warn', $_text, $_meta);
	}


	public static function error($_text, $_meta = [])
	{
		self::make('error', $_text, $_meta);
	}


	public static function direct($_direct = true)
	{
		self::$notif['direct'] = $_direct;
	}


	public static function redirect($_url)
	{
		self::$notif['redirect'] = $_url;
	}


	public static function result($_result)
	{
		self::$notif['result'] = $_result;
	}


	public static function compile($_json = false)
	{
		if($_json)
		{
			$return = json_encode(self::$notif);
		}
		else
		{
			$return = self::$notif;
		}
		return $return;
	}


	public static function get()
	{
		return self::$notif;
	}
}
?>