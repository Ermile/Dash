<?php
namespace dash\social\telegram;

class hook
{
	/**
	 * v3.0
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
		elseif(isset(tg::$hook['inline_query']['query']['id']))
		{
			$myDetection = tg::$hook['inline_query']['query']['id'];
		}
		elseif(isset(tg::$hook['chosen_inline_result']['inline_message_id']))
		{
			$myDetection = tg::$hook['chosen_inline_result']['inline_message_id'];
		}
		elseif(isset(tg::$hook['channel_post']['message_id']))
		{
			$myDetection = tg::$hook['channel_post']['message_id'];
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
		elseif(isset(tg::$hook['inline_query']))
		{
			$myDetection = tg::$hook['inline_query'];
		}
		elseif(isset(tg::$hook['chosen_inline_result']))
		{
			$myDetection = tg::$hook['chosen_inline_result'];
		}
		elseif(isset(tg::$hook['channel_post']))
		{
			$myDetection = tg::$hook['channel_post'];
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


	public static function inline_query($_arg = 'id')
	{
		$myDetection = null;
		if(isset(tg::$hook['inline_query']))
		{
			$myDetection = tg::$hook['inline_query'];
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


	public static function chosen_inline_result($_arg = 'inline_message_id')
	{
		$myDetection = null;
		if(isset(tg::$hook['chosen_inline_result']))
		{
			$myDetection = tg::$hook['chosen_inline_result'];
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


	public static function channel_post($_arg = 'message_id')
	{
		$myDetection = null;
		if(isset(tg::$hook['channel_post']))
		{
			$myDetection = tg::$hook['channel_post'];
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
		elseif(isset(tg::$hook['inline_query']['from']))
		{
			$myDetection = tg::$hook['inline_query']['from'];
		}
		elseif(isset(tg::$hook['chosen_inline_result']['from']))
		{
			$myDetection = tg::$hook['chosen_inline_result']['from'];
		}
		elseif(isset(tg::$hook['channel_post']))
		{
			$myDetection = null;
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
		elseif(isset(tg::$hook['inline_query']['from']))
		{
			$myDetection = tg::$hook['inline_query']['from'];
		}
		elseif(isset(tg::$hook['chosen_inline_result']['from']))
		{
			$myDetection = tg::$hook['chosen_inline_result']['from'];
		}
		elseif(isset(tg::$hook['channel_post']['chat']))
		{
			$myDetection = tg::$hook['channel_post']['chat'];
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


	public static function left_chat_member($_arg = 'id')
	{
		$myDetection = null;
		if(isset(tg::$hook['message']['left_chat_member']))
		{
			$myDetection = tg::$hook['message']['left_chat_member'];
		}
		elseif(isset(tg::$hook['callback_query']['message']['left_chat_member']))
		{
			$myDetection = tg::$hook['callback_query']['message']['left_chat_member'];
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
		elseif(isset(tg::$hook['inline_query']['query']))
		{
			$myDetection = 'iq_'.tg::$hook['inline_query']['query'];
		}
		elseif(isset(tg::$hook['chosen_inline_result']['query']))
		{
			$myDetection = 'iq_'.tg::$hook['chosen_inline_result']['query'];
		}
		elseif(isset(tg::$hook['channel_post']['caption']))
		{
			$myDetection = 'iq_'.tg::$hook['channel_post']['caption'];
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
	public static function cmd($_needle = null, $userInput = null)
	{
		if($userInput === null)
		{
			$userInput = self::text();
		}
		$text = trim($userInput);
		// if use callback or inline detect it and change text
		if(strpos($userInput , 'cb_') === 0)
		{
			$text = substr($userInput, 3);
		}
		elseif(strpos($userInput , 'iq_') === 0)
		{
			$text = substr($userInput, 3);
		}

		// define variable
		$cmd =
		[
			'text'        => $userInput,
			'detect'      => $text,
			'commandRaw'  => null,
			'command'     => null,
			'optionalRaw' => null,
			'optional'    => null,
			'argumentRaw' => null,
			'argument'    => null,
		];
		// seperate text by space
		$text = explode(' ', $userInput);
		// if we have parameter 1 save it as command
		if(isset($text[0]))
		{
			$cmd['commandRaw'] = $text[0];
			$cmd['command'] = mb_strtolower(trim($text[0]));
			if(strpos($cmd['command'], '@') !== false && strpos($cmd['command'], 'bot') !== false)
			{
				$cmd['command'] = strtok($cmd['command'], '@');
			}
			// if we have parameter 2 save it as optional
			if(isset($text[1]))
			{
				$cmd['optionalRaw'] = $text[1];
				$cmd['optional'] = mb_strtolower(trim($text[1]));
				// if we have parameter 3 save it as argument
				if(isset($text[2]))
				{
					$cmd['argumentRaw'] = $text[2];
					$cmd['argument'] = mb_strtolower(trim($text[2]));
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