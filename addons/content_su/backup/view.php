<?php
namespace content_su\backup;


class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();


		if(\dash\request::get('show') === 'log')
		{
			$this->data->auto_backup_log = @\dash\file::read(database. 'backup/log');
		}

		$config_backup = @\dash\file::read(database. 'backup/schedule');
		if($config_backup && is_string($config_backup))
		{
			$config_backup = json_decode($config_backup, true);
			$this->data->config_backup = $config_backup;
		}


		$this->data->mysql_info = \dash\db::global_status();

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
					'ago' => \dash\utility\human::timing(date("Y-m-d H:i:s", filemtime($value))),
				];
			}
			$old_backup_files = array_reverse($old_backup_files);
			$this->data->old_backup = $old_backup_files;
		}


		if(defined('db_log_name'))
		{
			$this->data->database_log = true;
		}
	}
}
?>