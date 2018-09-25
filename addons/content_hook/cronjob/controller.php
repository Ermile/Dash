<?php
namespace content_hook\cronjob;


class controller
{
	public static function routing()
	{
		if(\dash\permission::supervisor())
		{
			self::cronjob_run();
			return;
		}

		if(mb_strtoupper(\dash\request::is()) !== 'POST')
		{
			\dash\header::status(416);
		}

		$token = \dash\request::post('token');

		if(!$token)
		{
			\dash\code::boom();
		}

		$read_file = root. 'list.crontab.txt';
		if(is_file($read_file))
		{
			$check_token = file_get_contents($read_file);
			$check_token = json_decode($check_token, true);

			if(isset($check_token['token']) && $check_token['token'] === $token)
			{
				self::cronjob_run();
				return true;
				// this is ok
			}
		}
		\dash\code::boom();
	}


	private static function cronjob_run()
	{
		if(!\dash\option::config('cronjob','status'))
		{
			return;
		}

		$url = \dash\request::get('type');

		switch ($url)
		{
			// case 'notification':
			// 	\dash\app\sendnotification::send();
			// 	break;

			case 'closesolved':
				$time_now    = date("i");
				// every 10 minuts
				if((is_string($time_now) && mb_strlen($time_now) === 2 && $time_now{1} == '0') || \dash\permission::supervisor())
				{
					\dash\db\comments::close_solved_ticket();
				}
				break;

			default:
				// nothing
				break;
		}

		if(is_callable(['\lib\cronjob', 'run']))
		{
			\lib\cronjob::run();
		}
	}
}
?>