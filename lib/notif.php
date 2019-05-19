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
			if(is_string($_meta))
			{
				$_meta = ['element' => $_meta];
			}

			if(is_array($_meta))
			{
				if(array_key_exists('code', $_meta))
				{
					self::code($_meta['code']);
					unset($_meta['code']);
				}

				// if(!array_key_exists('image', $_meta))
				// {
				// 	if(\dash\option::config('notif', 'image'))
				// 	{
				// 		$_meta['image'] = \dash\url::site(). \dash\option::config('notif', 'image');
				// 	}
				// }
			}

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


	public static function api($_data)
	{
		self::pagination();
		self::result($_data);
		\dash\code::jsonBoom(self::$notif, true);
	}


	public static function code($_code)
	{
		self::add_detail('code', $_code);
	}


	private static function pagination()
	{
		$pagination = \dash\utility\pagination::api_detail();
		if($pagination)
		{
			self::add_detail('pagination', $pagination);
		}
	}


	public static function json()
	{
		return json_encode(self::$notif);
	}


	public static function get()
	{
		return self::$notif;
	}


	public static function clean()
	{
		self::$notif = [];
	}
}
?>