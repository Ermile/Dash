<?php
namespace content_hook\cronjob;


trait fn
{

	private static function master_cronjob()
	{
		// remove all expire session
		if(self::at('01:10'))
		{
			\dash\db\sessions::remove_old_expire();
		}
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

		// \dash\code::pretty($list, true);
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