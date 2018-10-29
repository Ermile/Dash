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
				// stop visitor save for cronjob
				\dash\temp::set('force_stop_visitor', true);

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
			case 'notification':
				$time = time();
				\dash\app\log\send::notification();
				if((time() - $time) < 20)
				{
					// run again
					sleep(20);
					\dash\app\log\send::notification();
				}
				break;

			case 'closesolved':
				if(self::every_10_min())
				{
					\dash\db\comments::close_solved_ticket();
				}
				break;

			case 'removetempfile':
				if(self::every_30_min())
				{
					self::removetempfile();
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


	public static function every_hour()
	{
		$time_now    = date("i");
		// every 1 hour
		if((is_string($time_now) && mb_strlen($time_now) === 2 && in_array($time_now, ['00'])) || \dash\permission::supervisor())
		{
			return true;
		}
		return false;
	}


	public static function every_30_min()
	{
		$time_now    = date("i");
		// every 30 minuts
		if((is_string($time_now) && mb_strlen($time_now) === 2 && in_array($time_now, ['00', '30'])) || \dash\permission::supervisor())
		{
			return true;
		}
		return false;
	}


	public static function every_10_min()
	{
		$time_now    = date("i");
		// every 10 minuts
		if((is_string($time_now) && mb_strlen($time_now) === 2 && $time_now{1} == '0') || \dash\permission::supervisor())
		{
			return true;
		}
		return false;
	}


	private static function removetempfile()
	{
		$addr = root. 'public_html/files/temp/';
		if(!\dash\file::exists($addr))
		{
			return;
		}

		$addr = \autoload::fix_os_path($addr);

		$glob = glob($addr. '*');

		$list = [];
		foreach ($glob as $key => $value)
		{
			if((time() - filemtime($value)) > (60*30))
			{
				\dash\file::delete($value);
				continue;
			}

			$list[] =
			[
				'download'  => \dash\url::site(). '/files/temp/'. basename($value),
				'name'      => basename($value),
				'remove_in' => (60*30) - (time() - filemtime($value)),
				'size'      => round((filesize($value)) / 1024, 2).  ' KB',
			];
		}

		\dash\code::pretty($list, true);

	}
}
?>