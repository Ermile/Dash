<?php
namespace content_hook\cronjob;


class controller
{
	use \content_hook\cronjob\times;
	use \content_hook\cronjob\fn;


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



	private static function cronjob_run()
	{
		if(!\dash\option::config('cronjob','status'))
		{
			return;
		}

		// this cronjob must be run every time
		self::master_cronjob();

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
			\lib\cronjob::run();
		}
	}

}
?>