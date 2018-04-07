<?php
namespace dash;
/**
 * Class for notif.
 */
class notif
{
	private static $notif = [];


	private static function add($_type, $_text, $_meta)
	{
		self::$notif['ok'] = \dash\engine\process::status();

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


	private static function add_detail($_key, $_value)
	{
		self::$notif['ok']  = \dash\engine\process::status();
		self::$notif[$_key] = $_value;
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
		\dash\engine\process::stop();

		self::add('error', $_text, $_meta);
	}


	public static function direct($_direct = true)
	{
		self::add_detail('direct', $_direct);
	}


	public static function redirect($_url)
	{
		self::add_detail('redirect', $_url);
	}


	public static function result($_result)
	{
		self::add_detail('result', $_result);
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