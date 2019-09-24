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

		// In local mode it should not be run
		if(\dash\url::isLocal())
		{
			return false;
		}

		if(\dash\url::child() !== 'exec')
		{
			\dash\header::status(404);
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

		if(!\dash\option::config('cronjob','status'))
		{
			\dash\header::status(403, 'Cronjob is off');
		}

		$read_file = core. 'lib/engine/cronjob/token.me.json';

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
		\dash\open::get();
		\dash\open::post();
		// this cronjob must be run every time
		self::master_cronjob();

		if(self::every_10_min())
		{
			self::expire_notif();
			\dash\db\comments::close_solved_ticket();
			\dash\utility\ip::check_is_block();
			\dash\db\comments::spam_by_block_ip();
		}

		if(self::every_30_min())
		{
			self::check_error_file();
			self::removetempfile();
		}

		\dash\app\log\send::notification();

		if(self::at('01:00'))
		{
			\dash\utility\dayevent::save();
		}

		if(is_callable(['\lib\cronjob', 'run']))
		{
			\lib\cronjob::run();
		}
	}

}
?>