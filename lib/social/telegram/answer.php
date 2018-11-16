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
			if(tg::isPrivate())
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
			else
			{
				// on public chats
				if(hook::chat('type') === 'channel')
				{
					// left from the channel
					tg::leaveChat();
				}
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
						}
						elseif(hook::new_chat_member('is_bot') === true)
						{
							$welcomeMsg .= T_("Hey Bot!"). "\n";
						}
						else
						{
							$welcomeMsg .= T_("How are you?"). "\n";
						}
						user::preview(hook::new_chat_member('id'), hook::new_chat_member(null), $welcomeMsg);
					}
					elseif(hook::left_chat_member('username'))
					{
						user::preview(hook::left_chat_member('id'), hook::left_chat_member(null), T_("Bye"));
					}
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

			T_("A mother is the truest friend we have"),
			T_("We have a lot of in common"),
			T_("You heard me"),
			T_("You were sublime"),
			T_("You heard me"),
			T_("A Younger idler, an old beggar"),
			T_("You can't put the clock back"),
			T_("Years teach us more than books"),
			T_("You can't keep a good man down"),
			T_("As easy as ABC"),
			T_("You saw nothing, you heard nothing"),
			T_("You took the words out of my mouth"),
			T_("You can educate a fool, but you can not make him think"),
			T_("You can't have your cake and eat it too"),
			T_("You can't take it with you when you die"),
			T_("You can not put out a fire with oil"),
			T_("You must lie on the bed you have made"),
			T_("You can't mend a broken egg"),
			T_("You can not get blood out of stone"),
			T_("You must grin and bear it"),
			T_("You can't please everyone"),

		];

		$randomAnswer = $myAnswerList[array_rand($myAnswerList)];

		return $randomAnswer;
	}
}
?>