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
		// first call fa
		$response = self::fa($_cmd);
		// if has no result call en
		if(!$response)
		{
			$response = self::en($_cmd);
		}
		if(!$response)
		{
			switch ($_cmd['command'])
			{
				case 'بگو':
				case 'say':
					$response = self::say($_cmd, true);
					break;

				case 'دبگو':
					$response = self::say($_cmd);
					break;

				default:
					break;
			}
		}

		return $response;
	}

	/**
	 * give input message and create best response for it
	 * @param  [type] $_cmd [description]
	 * @return [type]       [description]
	 */
	public static function fa($_cmd)
	{
		$text = null;

		switch ($_cmd['text'])
		{
			case 'سلام':
			case 'salam':
			case 'hallo':
				$text = 'سلام عزیزم';
				break;

			case 'خوبی':
			case 'khobi?':
			case 'khobi':
				$text = 'ممنون، خوبم';
				break;

			case 'خوبم':
			case 'خوبم?':
			case '/khobam?':
			case 'khobam?':
			case '/khobam':
			case 'khobam':
				$text = 'احتمالا خوب هستنید!';
				break;

			case 'چه خبرا':
			case 'چه خبرا?':
			case 'چخبر':
			case 'چخبر?':
			case 'چه خبر':
			case 'چه خبر?':
			case 'che khabar':
			case 'che khabar?':
				$text = 'سلامتی';
				break;

			case 'حالت خوبه':
				$text = 'عالی';
				break;

			case 'چاقی':
				$text = 'نه!';
				break;

			case 'سلامتی':
			case 'salamati':
			case 'salamati?':
				$text = 'خدا رو شکر';
				break;

			case 'بمیر':
				$text = 'مردن دست خداست';
				break;

			case 'بد':
				$text = 'من بد نیستم';
				break;

			case 'خوب':
				$text = 'ممنون';
				break;

			case 'زشت':
				$text = 'من خوشگلم';
				break;

			case 'هوا گرمه':
				$text = 'شاید!';
				break;

			case 'سردمه':
				$text = 'بخاری رو روشن کن';
				break;

			case 'بد':
				$text = 'من بد نیستم';
				break;

			case 'خر':
			case 'khar':
				$text = 'خر خودتی'."\r\n";
				$text .= 'باباته'."\r\n";
				$text .= 'بی تربیت'."\r\n";
				$text .= 'نزار چاک دهنم واشه'."\r\n";
				break;

			case 'سگ تو روحت':
			case 'sag to rohet':
			case 'sag to ruhet':
				$text = 'بله!'."\r\n";
				$text .= 'من روح ندارم!'."\r\n";
				break;

			case 'نفهم':
				$text = 'من خیلی هم میفهمم';
				break;

			case 'خوابی':
				$text = 'من همیشه بیدارم';
				break;

			case 'هی':
				$text = 'بفرمایید';
				break;

			case 'الو':
			case 'alo':
				$text = 'بله';
				break;

			case 'بلا':
				$text = 'با ادب باش';
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


	/**
	 * give input message and create best response for it
	 * @param  [type] $_cmd [description]
	 * @return [type]       [description]
	 */
	public static function en($_cmd)
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

			case 'userid':
			case 'user_id':
			case 'myid':
				$text = "\n\n<pre>". json_encode(\dash\social\telegram\hook::from(null), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE). "</pre>";
				break;

			case '/session':
			case 'session':
				$chatID = \dash\social\telegram\hook::from();
				if($chatID === 46898544 || $chatID === 344542267)
				{
					// temporary send tg result
					$_SESSION['tg'][date('Y-m-d H:i:s')] = '🔸 '. \dash\user::id();
					$text      = "\n\n<pre>". json_encode($_SESSION, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."</pre>";
				}
				else
				{
					$text = "Hi baby:)";
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


	/**
	 * repeat given word!
	 * @param  [type]  $_text [description]
	 * @param  boolean $_full [description]
	 * @return [type]         [description]
	 */
	public static function say($_text, $_repeat = false)
	{
		$text = $_text;
		if($_repeat && isset($_text['text']))
		{
			if(isset($_text['command']))
			{
				$len  = strlen($_text['command']);
				$text = substr($_text['text'], $len +1);
			}
		}
		// send message
		bot::sendMessage($text);

		return $text;
	}
}
?>