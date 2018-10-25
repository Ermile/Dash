<?php
namespace dash\social\telegram;

/** telegram generate needle library**/
class answer
{
	/**
	 * this library generate telegram tools
	 * v2.0
	 */

	public static function finder()
	{
		// check for step
		step::check(hook::text());
		if(tg::isOkay())
		{
			return true;
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
				call_user_func($funcName, hook::cmd());
				// if answer generated do not continue
				if(tg::isOkay())
				{
					break;
				}
			}
		}

		if(!tg::isOkay())
		{
			if(hook::chat('type') === 'group' || hook::chat('type') === 'supergroup')
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