<?php
namespace dash\social\telegram;

class hook extends tg
{


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






	/**
	 * handle response and return needed key if exist
	 * @param  [type] $_needle [description]
	 * @return [type]          [description]
	 */
	public static function analyse($_needle = null, $_arg = 'id')
	{
		$myDetection = null;

		switch ($_needle)
		{
			case 'update_id':
				if(isset(self::$hook['update_id']))
				{
					$myDetection = self::$hook['update_id'];
				}
				break;

			case 'message_id':
				if(isset(self::$hook['message']['message_id']))
				{
					$myDetection = self::$hook['message']['message_id'];
				}
				elseif(isset(self::$hook['callback_query']['message']['message_id']))
				{
					$myDetection = self::$hook['callback_query']['message']['message_id'];
				}
				break;

			case 'message':
				if(isset(self::$hook['message']))
				{
					$myDetection = self::$hook['message'];
				}
				elseif(isset(self::$hook['callback_query']['message']))
				{
					$myDetection = self::$hook['callback_query']['message'];
				}
				break;

			case 'callback_query_id':
				if(isset(self::$hook['callback_query']['id']))
				{
					$myDetection = self::$hook['callback_query']['id'];
				}
				break;

			case 'from':
				if(isset(self::$hook['message']['from']))
				{
					$myDetection = self::$hook['message']['from'];
				}
				elseif(isset(self::$hook['callback_query']['from']))
				{
					$myDetection = self::$hook['callback_query']['from'];
				}
				if($_arg)
				{
					if(isset($myDetection[$_arg]))
					{
						$myDetection = $myDetection[$_arg];
					}
					elseif($_arg !== null)
					{
						$myDetection = null;
					}
				}
				break;

			case 'chat':
			case 'new_chat_member':
			case 'new_chat_participant':
				if(isset(self::$hook['message'][$_needle]))
				{
					$myDetection = self::$hook['message'][$_needle];
				}
				elseif(isset(self::$hook['callback_query']['message'][$_needle]))
				{
					$myDetection = self::$hook['callback_query']['message'][$_needle];
				}
				if($_arg)
				{
					if(isset($myDetection[$_arg]))
					{
						$myDetection = $myDetection[$_arg];
					}
					elseif($_arg !== null)
					{
						$myDetection = null;
					}
				}
				break;

			case 'text':
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
					$myDetection = 'type_audio ';
				}
				elseif(isset(self::$hook['message']['document']))
				{
					$myDetection = 'type_document ';
				}
				elseif(isset(self::$hook['message']['photo']))
				{
					$myDetection = 'type_photo ';
				}
				elseif(isset(self::$hook['message']['sticker']))
				{
					$myDetection = 'type_sticker ';
				}
				elseif(isset(self::$hook['message']['video']))
				{
					$myDetection = 'type_video ';
				}
				elseif(isset(self::$hook['message']['voice']))
				{
					$myDetection = 'type_voice ';
				}
				elseif(isset(self::$hook['message']['venue']))
				{
					$myDetection = 'type_venue ';
				}

				// remove @bot_name
				$myDetection = str_replace('@'.self::$name, '', $myDetection);
				// trim text
				$myDetection = trim($myDetection);
				break;

			case 'contact':
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
					elseif($_arg !== null)
					{
						$myDetection = null;
					}
				}
				break;

			case 'location':
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
					elseif($_arg !== null)
					{
						$myDetection = null;
					}
				}
				break;

			default:
				break;
		}

		return $myDetection;
	}

}
?>