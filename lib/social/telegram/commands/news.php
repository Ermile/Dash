<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class news
{
	public static function run($_cmd)
	{
		switch ($_cmd['command'])
		{
			case '/news':
			case 'news':
			case '/News':
			case 'News':
			case T_("News"):
			case 'اخبار':
			case 'خبر':
				self::latest();
				return true;
				break;
		}

		return false;
	}


	private static function latest()
	{
		bot::ok();

		$siteTitle   = T_(\dash\option::config('site', 'title'));
		$apiUrl      = bot::website(). '/api/v6/posts?limit=10';
		$apiResponse = \dash\curl::go($apiUrl);

		$msg = '';
		$msg .= T_('Latest News'). ' '. $siteTitle. "\n\n";


		if(isset($apiResponse['result']) && is_array($apiResponse['result']))
		{
			foreach ($apiResponse['result'] as $key => $myPosts)
			{
				if(isset($myPosts['title']) && isset($myPosts['link']))
				{
					$msg .= '❇️ '. $myPosts['title']. "\n";
					$msg .= $myPosts['link']. "\n\n";
				}
			}
		}
		else
		{
			$msg .= '<code>'. T_('Not found!'). '</code>';
		}

		$result =
		[
			'text' => $msg,
			'reply_markup' =>
			[
				'inline_keyboard' =>
				[
					[
						[
							'text' => $siteTitle,
							'url' => bot::website(),
						],
					]
				]
			]
		];
		bot::sendMessage($result);
	}
}
?>