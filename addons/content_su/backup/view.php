<?php
namespace content_su\backup;


class view
{
	public static function config()
	{

		if(\dash\request::get('show') === 'log')
		{
			\dash\data::autoBackupLog(@\dash\file::read(database. 'backup/log'));
		}

		$configBackup = @\dash\file::read(database. 'backup/schedule');
		if($configBackup && is_string($configBackup))
		{
			$configBackup = json_decode($configBackup, true);
			\dash\data::configBackup($configBackup);
		}


		\dash\data::mysqlInfo(\dash\db::global_status());

		$oldBackup = @glob(database .'backup/files/*');

		$oldBackup_files = [];

		if($oldBackup && is_array($oldBackup))
		{
			foreach ($oldBackup as $key => $value)
			{
				$oldBackup_files [] =
				[
					'name' => basename($value),
					'time' => filemtime($value),
					'size' => round(filesize($value) / 1024 / 1024, 1),
					'date' => date("Y-m-d H:i:s", filemtime($value)),
					'ago' => \dash\utility\human::timing(date("Y-m-d H:i:s", filemtime($value))),
				];
			}
			$oldBackup_files = array_reverse($oldBackup_files);
			\dash\data::oldBackup($oldBackup_files);
		}


		if(defined('db_log_name'))
		{
			\dash\data::databaseLog(true);
		}
	}
}
?>