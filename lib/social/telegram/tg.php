<?php
namespace dash\social\telegram;

/** telegram **/
class tg
{
	/**
	 * this library get and send telegram messages
	 * v21.0
	 */
	public static $api_token   = null;
	public static $name        = null;
	public static $hook        = null;



	public static $language    = 'en_US';
	public static $cmd         = null;
	public static $cmdFolder   = null;
	public static $fill        = null;
	public static $defaultText = 'Undefined';
	public static $defaultMenu = null;
	public static $saveDest    = root.'public_html/files/telegram/';
	public static $AnswerOrder =
	[
		'handle',
		'callback',
		'user',
		'menu',
		'simple',
		'conversation',
	];

	/**
	 * execute telegram method
	 * @param  [type] $_name [description]
	 * @param  [type] $_args [description]
	 * @return [type]        [description]
	 */
	static function __callStatic($_name, $_args)
	{
		// try to detect json output
		$jsonResult = false;
		if(substr($_name, 0, 5) === 'json_')
		{
			$_name = substr($_name, 5);
			$jsonResult = true;
		}
		if(isset($_args[0]))
		{
			$_args = $_args[0];
		}
		if($_name)
		{
			return exec::send($_name, $_args, $jsonResult);
		}
		return false;
	}

	/**
	 * fire telegram api and run hook to get all requests
	 * @return [type] [description]
	 */
	public static function fire()
	{
		// if telegram is off then do not run
		if(!\dash\option::social('telegram', 'status'))
		{
			return T_('Telegram is off!');
		}
		// disable visitor loger
		\dash\temp::set('force_stop_visitor', true);
		// session_destroy();
		self::hook();
		// find answer for this message if need to answering
		answer::finder();
		// if we must pass result, we save it on result sending
		// now we need to save unanswered hook
		if(true)
		{
			// save log
			log::done();
		}
	}


	/**
	 * hook telegram messages. save and analyse user messages
	 * @param  boolean $_save [description]
	 * @return [type]         [description]
	 */
	public static function hook()
	{
		// get hook and save in static variable
		self::$hook     = json_decode(file_get_contents('php://input'), true);
		// save hook datetime
		log::hook();
		// force set session for this telegram user
		session::forceSet();
		// detect and set user id, access via \dash\user::id()
		user::detect();

		// detect cmd and save it in static value
		self::$cmd = self::cmdAnalyser(self::response('text'));
	}

	/**
	 * seperate input text to command
	 * @param  [type] $_input [description]
	 * @return [type]         [description]
	 */
	public static function cmdAnalyser($_input = null)
	{
		// define variable
		$cmd =
		[
			'text'     => null,
			'command'  => null,
			'optional' => null,
			'argument' => null,
		];
		// save input value as text
		$cmd['text'] = $_input;
		// seperate text by space
		$text = explode(' ', $_input);
		// if we have parameter 1 save it as command
		if(isset($text[0]))
		{
			$cmd['command'] = $text[0];
			// if we have parameter 2 save it as optional
			if(isset($text[1]))
			{
				$cmd['optional'] = $text[1];
				// if we have parameter 3 save it as argument
				if(isset($text[2]))
				{
					$cmd['argument'] = $text[2];
				}
			}
		}
		// return analysed text given from user
		return $cmd;
	}



	/**
	 * handle response and return needed key if exist
	 * @param  [type] $_needle [description]
	 * @return [type]          [description]
	 */
	public static function response($_needle = null, $_arg = 'id')
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