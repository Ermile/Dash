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
			\dash\notif::error("Token!");
			\dash\code::jsonBoom(\dash\notif::get());
		}

		$read_file = root. 'includes/cronjob/token.me.json';
		if(is_file($read_file))
		{
			$check_token = file_get_contents($read_file);
			$check_token = json_decode($check_token, true);

			if(isset($check_token['token']) && $check_token['token'] === $token)
			{
				// stop visitor save for cronjob
				\dash\temp::set('force_stop_visitor', true);

				// \dash\log::set('CronjobMasterOK');

				self::cronjob_run();

				// this is ok
				\dash\notif::ok("Ok ;)");
				\dash\code::jsonBoom(\dash\notif::get());

			}
		}
		\dash\log::set('CronjobTokenNotSet');
		\dash\notif::error("Token :/");
		\dash\code::jsonBoom(\dash\notif::get());

	}



	private static function sleep_until($_first_time, $_time)
	{
		$time_left = (time() - $_first_time);
		if($time_left < $_time)
		{
			\dash\code::sleep($_time - $time_left);
			return true;
		}

		return false;
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
			case 'system':

				if(self::every_10_min())
				{
					self::expire_notif();
				}

				if(self::every_30_min())
				{
					self::check_error_file();
				}
				break;

			case 'notification':
				$time = time();

				\dash\app\log\send::notification();

				// not sleep code in local
				if(\dash\url::isLocal())
				{
					break;
				}

				// if(self::sleep_until($time, 20))
				// {
				// 	\dash\app\log\send::notification();
				// }

				// if(self::sleep_until($time, 40))
				// {
				// 	\dash\app\log\send::notification();
				// }
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

				// cehck ip ic block or no
			case 'ipblocker';
				\dash\utility\ip::check_is_block();
				\dash\db\comments::spam_by_block_ip();
				break;

			case 'dayevent';
				if(self::at('01:00'))
				{
					\dash\utility\dayevent::save();
				}

				// if(self::at('07:07'))
				// {
				// 	\dash\utility\dayevent::day_notif();
				// }

				break;

			default:
				// nothing
				break;
		}

		if(is_callable(['\lib\cronjob', 'run']))
		{
			\dash\log::set('CronjobProjectRun');
			\lib\cronjob::run();
		}
	}


	public static function at($_time)
	{
		$time_now    = date("H:i");

		if((is_string($time_now) && $time_now === $_time) || \dash\permission::supervisor())
		{
			return true;
		}
		return false;
	}


	public static function at_00_clock()
	{
		$time_now    = date("H:i");
		// every 1 hour
		if((is_string($time_now) && $time_now === '00:00') || \dash\permission::supervisor())
		{
			return true;
		}
		return false;
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

	private static function expire_notif()
	{
		\dash\db\logs::expire_notif();
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


	private static function check_error_file()
	{
		$sqlError = root. 'includes/log/database/error.sql';
		if(is_file($sqlError))
		{
			\dash\log::set('su_sqlError');
		}

		$phpBug = root. 'includes/log/php/exception.log';
		if(is_file($phpBug))
		{
			\dash\log::set('su_phpBug');
		}
	}
}
?>