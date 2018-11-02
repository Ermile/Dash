<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class conversationFa
{
	public static function run($_cmd)
	{
		$text = null;
		$userInput = str_replace('?', '', $userInput);
		$userInput = str_replace('!', '', $userInput);
		$userInput = str_replace('؟', '', $userInput);
		$userInput = str_replace('*', '', $userInput);
		$userInput = str_replace('+', '', $userInput);
		$userInput = str_replace('-', '', $userInput);
		$userInput = str_replace('  ', '', $userInput);

		switch ($_cmd['text'])
		{
			case 'سلام':
			case 'سلااام':
			case 'salam':
			case 'hallo':
				$text = 'سلام عزیزم';
				break;

			case 'سلام خره':
			case 'سلام خر':
				$text = 'علیک سلام 😢 '. "\n". "توصبه میکنم با من با ادب صحبت کنید :|";
				break;

			case 'خوبی':
			case 'khobi':
				$text = 'ممنون، خوبم';
				break;

			case 'مرسی':
				$text = 'خیلی خرسی! فارسی صحبت کن جیگر'. "\n". "parlez-vous français?";
				break;

			case 'نه':
				$text = 'نه چرا! راضی باش';
				break;

			case 'نه والا':
				$text = 'آره والا چی میگی!';
				break;

			case 'بله':
				$text = 'نظر لطفتونه قربان';
				break;

			case 'ابله':
				$text = 'لطفا شان خودتون رو حفظ کنید';
				break;

			case 'خوبم':
			case 'khobam':
				$text = 'احتمالا خوب هستنید!';
				break;

			case 'چه خبرا':
			case 'چخبر':
			case 'چه خبر':
			case 'che khabar':
				$text = 'سلامتی';
				break;

			case 'حالت خوبه':
				$text = 'عالی هستم';
				break;

			case 'چرا':
			case 'چرا آخه':
				$text = 'چرا نداره عزیز من';
				break;


			case 'مقاله':
				$text = 'اینجا مقاله فروشی نیست! چی از من میخوای!!';
				break;

			case 'چاقی':
				$text = 'نه! چی در موردم فکر کردی!';
				break;

			case 'لاغری':
				$text = 'نخیر، من تناسب اندام دارم:|';
				break;

			case 'سلامتی':
			case 'salamati':
				$text = 'خدا رو شکر';
				break;

			case 'بمیر':
			case 'بمیری بهتره':
				$text = 'مردن دست خداست';
				break;

			case 'بد':
				$text = 'من بد نیستم';
				break;

			case 'خوب':
				$text = 'ممنون عزیزم';
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

			case 'جان':
				$text = 'جانت بی بلا عزیز جان';
				break;

			case 'خر':
			case 'خری':
			case 'خیلی خری':
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
			case 'خوابیدی':
				$text = 'من همیشه بیدارم';
				break;

			case 'هی':
				$text = 'بفرمایید جناب';
				break;

			case 'الو':
			case 'alo':
				$text = 'بله قربان';
				break;

			case 'چی میگی':
				$text = 'جز مدح شما نگویم!';
				break;

			case 'اسمت چیه':
			case 'اسم':
			case 'اسم شما':
			case 'اسم شما چیه':
				$text = 'بنده سرشمار هستم. فرزند ارمایل';
				break;

			case 'بلا':
				$text = 'با ادب باش';
				break;

			case 'دختری':
				$text = 'خودت چی فکر میکنی؟ بهم میاد 😜';
				break;


			case 'پسری':
				$text = 'بله، مشکلی هست؟';
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