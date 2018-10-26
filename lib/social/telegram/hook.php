<?php
namespace dash\social\telegram;

class hook
{
	/**
	 * v2.0
	 * this class try to detect all part of hook and return related value
	 */

	public static function update_id()
	{
		$myDetection = null;
		if(isset(tg::$hook['update_id']))
		{
			$myDetection = tg::$hook['update_id'];
		}
		return $myDetection;
	}


	public static function message_id()
	{
		$myDetection = null;
		if(isset(tg::$hook['message']['message_id']))
		{
			$myDetection = tg::$hook['message']['message_id'];
		}
		elseif(isset(tg::$hook['callback_query']['message']['message_id']))
		{
			$myDetection = tg::$hook['callback_query']['message']['message_id'];
		}
		return $myDetection;
	}


	public static function message($_arg = null)
	{
		$myDetection = null;
		if(isset(tg::$hook['message']))
		{
			$myDetection = tg::$hook['message'];
		}
		elseif(isset(tg::$hook['callback_query']['message']))
		{
			$myDetection = tg::$hook['callback_query']['message'];
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


	public static function callback_query($_arg = 'id')
	{
		$myDetection = null;
		if(isset(tg::$hook['callback_query']))
		{
			$myDetection = tg::$hook['callback_query'];
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
		if(isset(tg::$hook['message']['from']))
		{
			$myDetection = tg::$hook['message']['from'];
		}
		elseif(isset(tg::$hook['callback_query']['from']))
		{
			$myDetection = tg::$hook['callback_query']['from'];
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
		if(isset(tg::$hook['message']['chat']))
		{
			$myDetection = tg::$hook['message']['chat'];
		}
		elseif(isset(tg::$hook['callback_query']['message']['chat']))
		{
			$myDetection = tg::$hook['callback_query']['message']['chat'];
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
		if(isset(tg::$hook['message']['new_chat_member']))
		{
			$myDetection = tg::$hook['message']['new_chat_member'];
		}
		elseif(isset(tg::$hook['callback_query']['message']['new_chat_member']))
		{
			$myDetection = tg::$hook['callback_query']['message']['new_chat_member'];
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
		if(isset(tg::$hook['message']['new_chat_participant']))
		{
			$myDetection = tg::$hook['message']['new_chat_participant'];
		}
		elseif(isset(tg::$hook['callback_query']['message']['new_chat_participant']))
		{
			$myDetection = tg::$hook['callback_query']['message']['new_chat_participant'];
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
		if(isset(tg::$hook['message']['text']))
		{
			$myDetection = tg::$hook['message']['text'];
		}
		elseif(isset(tg::$hook['callback_query']['data']))
		{
			$myDetection = 'cb_'.tg::$hook['callback_query']['data'];
		}
		elseif(isset(tg::$hook['message']['contact'])
			&& isset(tg::$hook['message']['contact']['phone_number'])
		)
		{
			if(isset(tg::$hook['message']['contact']['fake']))
			{
				$myDetection = 'type_contact '. tg::$hook['message']['contact']['phone_number'] .' fake';
			}
			else
			{
				$myDetection = 'type_contact '. tg::$hook['message']['contact']['phone_number'];
			}
		}
		elseif(isset(tg::$hook['message']['location'])
			&& isset(tg::$hook['message']['location']['longitude'])
			&& isset(tg::$hook['message']['location']['latitude'])
		)
		{
			$myDetection = 'type_location ';
			$myDetection .= tg::$hook['message']['location']['longitude']. ' ';
			$myDetection .= tg::$hook['message']['location']['latitude'];
		}
		elseif(isset(tg::$hook['message']['audio']))
		{
			$myDetection = 'type_audio';
		}
		elseif(isset(tg::$hook['message']['document']))
		{
			$myDetection = 'type_document';
		}
		elseif(isset(tg::$hook['message']['photo']))
		{
			$myDetection = 'type_photo';
		}
		elseif(isset(tg::$hook['message']['sticker']))
		{
			$myDetection = 'type_sticker';
		}
		elseif(isset(tg::$hook['message']['video']))
		{
			$myDetection = 'type_video';
		}
		elseif(isset(tg::$hook['message']['voice']))
		{
			$myDetection = 'type_voice';
		}
		elseif(isset(tg::$hook['message']['venue']))
		{
			$myDetection = 'type_venue';
		}

		if($_removeBotName && tg::$name)
		{
			// remove @bot_name
			$myDetection = str_replace('@'.tg::$name, '', $myDetection);
		}
		// trim text
		$myDetection = trim($myDetection);

		return $myDetection;
	}


	/**
	 * seperate input text to command
	 * @return [type]         [description]
	 */
	public static function cmd($_needle = null)
	{
		// define variable
		$cmd =
		[
			'text'     => self::text(),
			'command'  => null,
			'optional' => null,
			'argument' => null,
		];
		// seperate text by space
		$text = explode(' ', self::text());
		// if we have parameter 1 save it as command
		if(isset($text[0]))
		{
			$cmd['command'] = mb_strtolower($text[0]);
			// if we have parameter 2 save it as optional
			if(isset($text[1]))
			{
				$cmd['optional'] = mb_strtolower($text[1]);
				// if we have parameter 3 save it as argument
				if(isset($text[2]))
				{
					$cmd['argument'] = mb_strtolower($text[2]);
				}
			}
		}
		if($_needle)
		{
			if(isset($cmd[$_needle]))
			{
				$cmd = $cmd[$_needle];
			}
			else
			{
				$cmd = null;
			}
		}
		// return analysed text given from user
		return $cmd;
	}


	public static function contact($_arg = 'id')
	{
		$myDetection = null;
		if(isset(tg::$hook['message']['contact']))
		{
			$myDetection = tg::$hook['message']['contact'];
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
		if(isset(tg::$hook['message']['location']))
		{
			$myDetection = tg::$hook['message']['location'];
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