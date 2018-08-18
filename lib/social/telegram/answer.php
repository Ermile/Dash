<?php
namespace dash\social\telegram;

/** telegram generate needle library**/
class answer extends tg
{
	/**
	 * this library generate telegram tools
	 * v2.0
	 */

	public static function finder()
	{
		// try to run classes based on order list
		foreach (tg::$AnswerOrder as $myClass)
		{
			if(substr($myClass, 0, 5) === 'dash:')
			{
				$myClass = '\\' .__NAMESPACE__.'\commands\\'. substr($myClass, 5);
			}

			$funcName = $myClass.'::run';
			// generate func name
			if(is_callable($funcName))
			{
				// call this class main fn
				$answer = call_user_func($funcName, hook::cmd());
				// if has response break loop
				if($answer || is_array($answer))
				{
					break;
				}
			}
		}

		// // temporary send tg result
		// $_SESSION['tg'][self::$hookDate] = 'salam '. \dash\user::id() ;
		// $msg      = "\n\n<pre>". json_encode($_SESSION, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."</pre>";
		// $myData   = ['text' => 'Salaaaam '. hook::from('first_name'). $msg];
		// $myResult = \dash\social\telegram\tg::json_sendMessage($myData);
	}



	/**
	 * default action to handle message texts
	 * @param  [type] [description]
	 * @return [type]       [description]
	 */
	public static function answer($forceSample = null)
	{
		$answer  = null;
		// read from main command template
		$cmdFolder = __NAMESPACE__ .'\commands\\';

		// use user defined command
		if(!$forceSample && self::$cmdFolder)
		{
			$cmdFolder = self::$cmdFolder;
		}
		foreach (self::$priority as $class)
		{
			$funcName = $cmdFolder. $class.'::exec';
			// generate func name
			if(is_callable($funcName))
			{
				// get response
				$answer = call_user_func($funcName, self::$cmd);
				// if has response break loop
				if($answer || is_array($answer))
				{
					break;
				}
			}
		}
		// if we dont have answer text then use default text
		if(!$answer)
		{
			if(self::response('chat', 'type') === 'group')
			{
				// if your bot joied to group show thanks message
				if(self::response('new_chat_member', 'username') === self::$name)
				{
					$msg = "Thanks for using me!\r\n\nI'm Bot.";
					$msg = "با تشکر از شما عزیزان به خاطر دعوت از من!\r\n\nمن یک ربات هستم.";
					$answer = ['text' => $msg ];
				}
			}
			elseif(\dash\option::social('telegram', 'debug') && !is_array($answer))
			{
				// then if not exist set default text
				$answer = ['text' => self::$defaultText];
				if(self::$defaultMenu && is_object(self::$defaultMenu))
				{
					$answer['reply_markup'] = call_user_func(self::$defaultMenu);
				}
			}
		}
		return $answer;
	}
}
?>