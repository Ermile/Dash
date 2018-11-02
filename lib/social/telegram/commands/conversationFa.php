<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class conversationFa
{
	public static function run($_cmd)
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
				$text = 'خر خودتی'."\n";
				$text .= 'بی تربیت'."\n";
				$text .= 'نزار چاک دهنم واشه'."\n";
				break;

			case 'سگ تو روحت':
			case 'sag to rohet':
			case 'sag to ruhet':
				$text = 'بله!'."\n";
				$text .= 'من روح ندارم!'."\n";
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
			bot::ok();
		}
	}
}
?>