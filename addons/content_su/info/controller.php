<?php
namespace content_su\info;

class controller
{
	public static function routing()
	{
		$name = \dash\url::dir(1);

		if(!$name)
		{
			return;
		}

		switch ($name)
		{
			case 'php':
				phpinfo();
				break;


			case 'server':
				$exist = true;
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && !class_exists("COM"))
				{
					ob_start();
					echo "<!DOCTYPE html><meta charset='UTF-8'/><title>Extract text form twig files</title><body style='padding:0 1%;margin:0 1%;direction:ltr;overflow:hidden'>";

					echo "<h1>". T_("First you need to enable COM on windows")."</h1>";
					echo "<a target='_blank' href='http://www.php.net/manual/en/class.com.php'>" . T_("Read More") . "</a>";
					break;
				}

				require addons.'lib/linfo/index.php';

				break;


			default:
				echo "Nothing!";
				break;
		}

		\dash\code::boom();
	}
}
?>