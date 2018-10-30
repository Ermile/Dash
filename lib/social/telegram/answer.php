<?php
namespace dash\social\telegram;

/** telegram generate needle library**/
class answer
{
	/**
	 * this library generate telegram tools
	 * v3.0
	 */

	public static function finder($_cmd = null)
	{
		if(!$_cmd)
		{
			$_cmd = hook::cmd();
		}
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
				call_user_func($funcName, $_cmd);
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
				if(hook::new_chat_member('username'))
				{
					$welcomeMsg = T_("Hello")."!!\n";

					if(hook::new_chat_member('username') === tg::$name)
					{
						$welcomeMsg .= T_("Thanks for using me!")."\n";
						$welcomeMsg .= T_("I'm Bot.");
						// send this as message
						tg::sendMessage($welcomeMsg);
					}
					elseif(hook::new_chat_member('is_bot') === true)
					{
						$welcomeMsg .= T_("Hey Bot!"). "\n";
						$welcomeMsg .= "<code>". hook::new_chat_member('first_name') ."</code>". "\n";
						$welcomeMsg .= "@". hook::new_chat_member('username');
					}
					else
					{
						$welcomeMsg .= T_("How are you?"). "\n";
						$welcomeMsg .= "<code>". hook::new_chat_member('first_name') ."</code>". "\n";
						$welcomeMsg .= "@". hook::new_chat_member('username');
					}
					user::preview(hook::new_chat_member('id'), hook::new_chat_member(null));
					// send welcome message
					// tg::sendMessage($welcomeMsg);
				}
				elseif(hook::left_chat_member('username'))
				{
					$welcomeMsg = T_("Bye").":/\n";
					$welcomeMsg .= "<code>". hook::left_chat_member('first_name') ."</code>". "\n";
					$welcomeMsg .= "@". hook::left_chat_member('username');
					// send bye message
					tg::sendMessage($welcomeMsg);
				}

			}
			else
			{
				// then if not exist set default text
				$answer = ['text' => self::randomAnswer()];
				// if start with callback answer callback
				if(tg::isCallback())
				{
					tg::answerCallbackQuery($answer);
				}
				else
				{
					tg::sendMessage($answer);
				}
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