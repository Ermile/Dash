<?php
namespace addons\content_su\backup;
use \lib\utility;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();


		if(\lib\utility::get('show') === 'log')
		{
			$this->data->auto_backup_log = @\lib\utility\file::read(database. 'backup/log');
		}

		$config_backup = @\lib\utility\file::read(database. 'backup/schedule');
		if($config_backup && is_string($config_backup))
		{
			$config_backup = json_decode($config_backup, true);
			$this->data->config_backup = $config_backup;
		}


		$this->data->mysql_info = \lib\db::global_status();

		$old_backup = @glob(database .'backup/files/*');

		$old_backup_files = [];

		if($old_backup && is_array($old_backup))
		{
			foreach ($old_backup as $key => $value)
			{
				$old_backup_files [] =
				[
					'name' => basename($value),
					'time' => filemtime($value),
					'size' => round(filesize($value) / 1024 / 1024, 1),
					'date' => date("Y-m-d H:i:s", filemtime($value)),
					'ago' => \lib\utility::humanTiming(date("Y-m-d H:i:s", filemtime($value))),
				];
			}
			$old_backup_files = array_reverse($old_backup_files);
			$this->data->old_backup = $old_backup_files;
		}

	}
}
?>