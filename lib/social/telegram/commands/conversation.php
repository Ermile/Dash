<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class conversation
{
	/**
	 * execute user request and return best result
	 * @param  [type] $_cmd [description]
	 * @return [type]       [description]
	 */
	public static function run($_cmd)
	{
		$text = null;

		switch ($_cmd['text'])
		{
			case 'hello':
				$text = 'hello!';
				break;

			case 'good':
			case '/howami':
			case 'howami':
			case 'ls':
			case 'ls-la':
			case 'ls-a':
				$text = ':)';
				break;

			case 'bad':
				$text = ':(';
				break;

			case '/fuck':
			case 'fuck':
			case 'f*ck':
				$text = "YOU ARE A PROGRAMMER🍆";
				break;

			case 'how are you':
			case 'how are you?':
				$text = "I'm fine, thanks";
				break;

			case 'test':
				$text = T_('Test <b>:name</b> bot on :site', ['name' => bot::$name, 'site' => \dash\url::kingdom()]);
				break;

			case '/userid':
			case 'userid':
			case '/user_id':
			case 'user_id':
			case '/myid':
			case 'myid':
				$text = T_("User id"). ' '. \dash\user::id();
				$text .= "\n\n<pre>". json_encode(\dash\social\telegram\hook::from(null), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE). "</pre>";
				break;

			case '/session':
			case 'session':
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


			case '/logout':
			case 'logout':
					bot::sendMessage('📴 '.T_("Booom"));
					\dash\user::destroy();
					\dash\code::boom();
				break;


			case '/tgsession':
			case 'tgsession':
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


			default:
				$text = false;
				break;
		}
		if($text)
		{
			bot::sendMessage($text);
		}
		// return response as result
		return $text;
	}
}
?>