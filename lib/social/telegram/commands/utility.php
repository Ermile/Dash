<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class utility
{
	/**
	 * execute user request and return best result
	 * @param  [type] $_cmd [description]
	 * @return [type]       [description]
	 */
	public static function run($_cmd)
	{
		$text = null;

		switch ($_cmd['command'])
		{
			case '/userid':
			case 'userid':
			case 'cb_userid':
			case '/user_id':
			case 'user_id':
			case '/myid':
			case 'myid':
				\dash\log::set('tg:user:userid');

				// if start with callback answer callback
				if(bot::isCallback())
				{
					$callbackResult =
					[
						'text' => T_("More and more"),
					];
					bot::answerCallbackQuery($callbackResult);
				}

				$text = T_("User id"). ' <code>'. \dash\user::id(). '</code>';
				$text .= "\n\n<pre>". json_encode(\dash\social\telegram\hook::from(null), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE). "</pre>";
				break;


			case '/session':
			case 'session':
				\dash\log::set('tg:user:session');

				$chatID = \dash\social\telegram\hook::from();
				if($chatID === 46898544 || $chatID === 344542267 || $chatID === 33263188)
				{
					// temporary send tg result
					$_SESSION['tg'][date('Y-m-d H:i:s')] = '🔸 '. \dash\user::id();
					$text      = "\n\n<pre>". json_encode($_SESSION, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."</pre>";
				}
				else
				{
					$text = "Hello my son:)";
				}
				break;


			case '/delete':
				\dash\log::set('tg:user:deleteTry');

				$chatID = \dash\social\telegram\hook::from();
				if($chatID === 46898544 || $chatID === 344542267 || $chatID === 33263188)
				{
					bot::sendMessage('💔 '.T_("Wow"));
					\dash\user::destroy();
					\dash\app\tg\user::hard_delete($chatID);
					\dash\code::boom();
				}
				else
				{
					$text = T_("You cannot delete your account from Telegram!");
				}
				break;


			case '/logout':
			case 'logout':
					\dash\log::set('tg:user:logout');

					bot::sendMessage('📴 '.T_("Booom"));
					\dash\user::destroy();
					\dash\code::boom();
				break;


			case '/tgsession':
			case 'tgsession':
				\dash\log::set('tg:user:tgsession');

				$chatID = \dash\social\telegram\hook::from();
				if($chatID === 46898544 || $chatID === 344542267 || $chatID === 33263188)
				{
					// temporary send tg result
					$_SESSION['tg'][date('Y-m-d H:i:s')] = '🔸 '. \dash\user::id();
					$text      = "\n\n<pre>". json_encode($_SESSION['tg'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."</pre>";
				}
				else
				{
					$text = "Hi tg baby:)";
				}
				break;


			case '/say':
			case T_('say'):

					if(isset($_cmd['text']))
					{
						$len  = strlen($_cmd['command']);
						$text = substr($_cmd['text'], $len +1);
					}
				break;


			case 'دبگو':
				$text = $_cmd['text'];
				break;
		}

		if($text)
		{
			bot::sendMessage($text);
			bot::ok();
		}
	}
}
?>