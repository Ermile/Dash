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

			case 'hi':
				$text = 'hi dear!';
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

			case '🍆':
				$text = "🍆🍆 👍";
				break;

			case 'how are you':
			case 'how are you?':
				$text = "I'm fine, thanks";
				break;

			case 'test':
				$text = T_('Test <b>:name</b> bot on :site', ['name' => bot::$name, 'site' => bot::website()]);
				break;


			default:
				$text = false;
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