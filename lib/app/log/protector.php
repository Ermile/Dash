<?php
namespace dash\app\log;


class protector
{
	private static $max_exec_cronjob_time = 30;
	private static $max_tg_per_sec        = 10;

	private static $tg_sec                = [];
	private static $tg_chatid_sec         = [];

	// max 30 msg per second
	// max 1 msg per user

	public static function tg($_chat_id)
	{
		$chat_id = null;
		if(isset($_chat_id['chat_id']))
		{
			$chat_id = $_chat_id['chat_id'];
		}
		elseif(is_numeric($_chat_id) || is_string($_chat_id))
		{
			$chat_id = $_chat_id;
		}

		self::tg_new_msg($chat_id);

		return self::check($chat_id);
	}

	public static function cronjob($_time)
	{
		if(time() - intval($_time) >= self::$max_exec_cronjob_time)
		{
			return false;
		}

		return true;
	}


	private static function check($_chat_id = null)
	{
		$time = time();
		if(isset(self::$tg_sec[$time]))
		{
			if(intval(self::$tg_sec[$time]) >= self::$max_tg_per_sec)
			{
				return false;
			}
		}

		if($_chat_id)
		{
			if(isset(self::$tg_chatid_sec[$time][$_chat_id]))
			{
				\dash\code::sleep(1);
			}
		}

		return true;
	}


	private static function tg_new_msg($_chat_id)
	{
		$time = time();
		if(!isset(self::$tg_sec[$time]))
		{
			self::$tg_sec[$time] = 0;
		}

		self::$tg_sec[$time]++;

		if(!isset(self::$tg_chatid_sec[$time][$_chat_id]))
		{
			self::$tg_chatid_sec[$time][$_chat_id] = 0;
		}

		self::$tg_chatid_sec[$time][$_chat_id]++;

	}

}
?>