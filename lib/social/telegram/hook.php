<?php
namespace dash\social\telegram;

class hook extends tg
{
	/**
	 * v2.0
	 * this class try to detect all part of hook and return related value
	 */

	public static function update_id()
	{
		$myDetection = null;
		if(isset(self::$hook['update_id']))
		{
			$myDetection = self::$hook['update_id'];
		}
		return $myDetection;
	}


	public static function message_id()
	{
		$myDetection = null;
		if(isset(self::$hook['message']['message_id']))
		{
			$myDetection = self::$hook['message']['message_id'];
		}
		elseif(isset(self::$hook['callback_query']['message']['message_id']))
		{
			$myDetection = self::$hook['callback_query']['message']['message_id'];
		}
		return $myDetection;
	}


	public static function message()
	{
		$myDetection = null;
		if(isset(self::$hook['message']))
		{
			$myDetection = self::$hook['message'];
		}
		elseif(isset(self::$hook['callback_query']['message']))
		{
			$myDetection = self::$hook['callback_query']['message'];
		}
		return $myDetection;
	}


	public static function callback_query($_arg = 'id')
	{
		$myDetection = null;
		if(isset(self::$hook['callback_query']))
		{
			$myDetection = self::$hook['callback_query'];
		}
		// get only arg
		if($_arg)
		{
			if(isset($myDetection[$_arg]))
			{
				$myDetection = $myDetection[$_arg];
			}
			else
			{
				$myDetection = null;
			}
		}
		return $myDetection;
	}


	public static function from($_arg = 'id')
	{
		$myDetection = null;
		if(isset(self::$hook['message']['from']))
		{
			$myDetection = self::$hook['message']['from'];
		}
		elseif(isset(self::$hook['callback_query']['from']))
		{
			$myDetection = self::$hook['callback_query']['from'];
		}
		// get only arg
		if($_arg)
		{
			if(isset($myDetection[$_arg]))
			{
				$myDetection = $myDetection[$_arg];
			}
			else
			{
				$myDetection = null;
			}
		}
		return $myDetection;
	}


	public static function chat($_arg = 'id')
	{
		$myDetection = null;
		if(isset(self::$hook['message']['chat']))
		{
			$myDetection = self::$hook['message']['chat'];
		}
		elseif(isset(self::$hook['callback_query']['message']['chat']))
		{
			$myDetection = self::$hook['callback_query']['message']['chat'];
		}
		// get only arg
		if($_arg)
		{
			if(isset($myDetection[$_arg]))
			{
				$myDetection = $myDetection[$_arg];
			}
			else
			{
				$myDetection = null;
			}
		}
		return $myDetection;
	}


	public static function new_chat_member($_arg = 'id')
	{
		$myDetection = null;
		if(isset(self::$hook['message']['new_chat_member']))
		{
			$myDetection = self::$hook['message']['new_chat_member'];
		}
		elseif(isset(self::$hook['callback_query']['message']['new_chat_member']))
		{
			$myDetection = self::$hook['callback_query']['message']['new_chat_member'];
		}
		// get only arg
		if($_arg)
		{
			if(isset($myDetection[$_arg]))
			{
				$myDetection = $myDetection[$_arg];
			}
			else
			{
				$myDetection = null;
			}
		}
		return $myDetection;
	}


	public static function new_chat_participant($_arg = 'id')
	{
		$myDetection = null;
		if(isset(self::$hook['message']['new_chat_participant']))
		{
			$myDetection = self::$hook['message']['new_chat_participant'];
		}
		elseif(isset(self::$hook['callback_query']['message']['new_chat_participant']))
		{
			$myDetection = self::$hook['callback_query']['message']['new_chat_participant'];
		}
		// get only arg
		if($_arg)
		{
			if(isset($myDetection[$_arg]))
			{
				$myDetection = $myDetection[$_arg];
			}
			else
			{
				$myDetection = null;
			}
		}
		return $myDetection;
	}


	public static function text($_removeBotName = true)
	{
		$myDetection = null;
		if(isset(self::$hook['message']['text']))
		{
			$myDetection = self::$hook['message']['text'];
		}
		elseif(isset(self::$hook['callback_query']['data']))
		{
			$myDetection = 'cb_'.self::$hook['callback_query']['data'];
		}
		elseif(isset(self::$hook['message']['contact'])
			&& isset(self::$hook['message']['contact']['phone_number'])
		)
		{
			if(isset(self::$hook['message']['contact']['fake']))
			{
				$myDetection = 'type_contact '. self::$hook['message']['contact']['phone_number'] .' fake';
			}
			else
			{
				$myDetection = 'type_contact '. self::$hook['message']['contact']['phone_number'];
			}
		}
		elseif(isset(self::$hook['message']['location'])
			&& isset(self::$hook['message']['location']['longitude'])
			&& isset(self::$hook['message']['location']['latitude'])
		)
		{
			$myDetection = 'type_location ';
			$myDetection .= self::$hook['message']['location']['longitude']. ' ';
			$myDetection .= self::$hook['message']['location']['latitude'];
		}
		elseif(isset(self::$hook['message']['audio']))
		{
			$myDetection = 'type_audio';
		}
		elseif(isset(self::$hook['message']['document']))
		{
			$myDetection = 'type_document';
		}
		elseif(isset(self::$hook['message']['photo']))
		{
			$myDetection = 'type_photo';
		}
		elseif(isset(self::$hook['message']['sticker']))
		{
			$myDetection = 'type_sticker';
		}
		elseif(isset(self::$hook['message']['video']))
		{
			$myDetection = 'type_video';
		}
		elseif(isset(self::$hook['message']['voice']))
		{
			$myDetection = 'type_voice';
		}
		elseif(isset(self::$hook['message']['venue']))
		{
			$myDetection = 'type_venue';
		}

		if($_removeBotName)
		{
			// remove @bot_name
			$myDetection = str_replace('@'.self::$name, '', $myDetection);
		}
		// trim text
		$myDetection = trim($myDetection);

		return $myDetection;
	}


	public static function contact($_arg = 'id')
	{
		$myDetection = null;
		if(isset(self::$hook['message']['contact']))
		{
			$myDetection = self::$hook['message']['contact'];
		}
		if($_arg)
		{
			if(isset($myDetection[$_arg]))
			{
				$myDetection = $myDetection[$_arg];
			}
			else
			{
				$myDetection = null;
			}
		}
		return $myDetection;
	}


	public static function location($_arg = 'id')
	{
		$myDetection = null;
		if(isset(self::$hook['message']['location']))
		{
			$myDetection = self::$hook['message']['location'];
		}
		if($_arg)
		{
			if(isset($myDetection[$_arg]))
			{
				$myDetection = $myDetection[$_arg];
			}
			else
			{
				$myDetection = null;
			}
		}
		return $myDetection;
	}
}
?>