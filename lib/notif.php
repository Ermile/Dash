<?php
namespace lib;
/**
 * Class for notif.
 */
class notif
{
	private static $notif = [];
	private static $ok    = true;

	private static function add($_type, $_text, $_meta)
	{
		self::$notif['ok'] = \lib\engine\process::status();

		if(!isset(self::$notif['msg']))
		{
			self::$notif['msg'] = [];
		}

		$add =
		[
			'type' => $_type,
			'text' => $_text,
		];

		if($_meta)
		{
			$add['meta'] = $_meta;
		}

		array_push(self::$notif['msg'], $add);
	}


	public static function info($_text, $_meta = [])
	{
		self::add('info', $_text, $_meta);
	}


	public static function ok($_text, $_meta = [])
	{
		self::add('ok', $_text, $_meta);
	}


	public static function warn($_text, $_meta = [])
	{
		self::add('warn', $_text, $_meta);
	}


	public static function error($_text, $_meta = [])
	{
		// stop engine process
		\lib\engine\process::stop();

		self::add('error', $_text, $_meta);
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


	public static function json()
	{
		return json_encode(self::$notif);
	}


	public static function get()
	{
		return self::$notif;
	}
}
?>