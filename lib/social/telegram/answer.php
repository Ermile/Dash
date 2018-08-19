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
		$answer  = null;

		// check for step
		$response = step::check(hook::text());
		if($response)
		{
			return $response;
		}

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

		if(!$answer)
		{
			if(hook::chat('type') === 'group')
			{
				// if your bot joied to group show thanks message
				if(hook::new_chat_member('username') === self::$name)
				{
					$msg = T_("Thanks for using me!")."\r\n\n";
					$msg .= T_("I'm Bot.");
					// send this as message
					tg::sendMessage($msg);
				}
			}
			else
			{
				// then if not exist set default text
				$answer = ['text' => self::randomAnswer()];
				if(tg::$defaultMenu && is_object(tg::$defaultMenu))
				{
					$answer['reply_markup'] = call_user_func(tg::$defaultMenu);
				}
				tg::sendMessage($answer);
			}
		}

		// // temporary send tg result
		// $_SESSION['tg'][self::$hookDate] = 'salam '. \dash\user::id() ;
		// $msg      = "\n\n<pre>". json_encode($_SESSION, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."</pre>";
		// $myData   = ['text' => 'Salaaaam '. hook::from('first_name'). $msg];
		// $myResult = \dash\social\telegram\tg::json_sendMessage($myData);
	}


	/**
	 * generate random answer when no answer is exist for this message
	 * @return [type] [description]
	 */
	public static function randomAnswer()
	{
		$myAnswerList =
		[
			T_("Hey 😀"),
			T_("What's up?"),
			T_("Tell me a joke!"),
			T_("Surprise me!"),
			T_("How are you?"),
			T_("How old are you?"),
		];

		$randomAnswer = $myAnswerList[array_rand($myAnswerList)];

		return $randomAnswer;
	}
}
?>