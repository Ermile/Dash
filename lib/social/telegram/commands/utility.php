<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class utitlity
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


			case '/say':
			case 'say':
			case 'بگو':
					$response = self::say($_cmd, true);
					if(isset($ـ_cmd['text']))
					{
						$len  = strlen($ـ_cmd['command']);
						$text = substr($ـ_cmd['text'], $len +1);
					}
				break;


			case 'دبگو':
				$text = $text;
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