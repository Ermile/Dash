<?php
namespace addons\content_su\backup;

class model extends \addons\content_su\main\model
{
	public function post_backup()
	{
		if(\dash\request::post('backup') === 'now')
		{
			$this->backup_now();
		}
		elseif(\dash\request::post('backup') === 'now_log')
		{
			if(defined('db_log_name'))
			{
				$this->backup_now(db_log_name);
			}
			else
			{
				\dash\notif::error(T_("Database of logs dose not exists"));
				return false;
			}
		}
		elseif(\dash\request::post('backup') === 'schedule')
		{
			$this->backup_schedule();
		}
		elseif(\dash\request::post('type') === 'remove' && \dash\request::post('file'))
		{
			$file_name = \dash\request::post('file');
			if(\dash\file::delete(database. 'backup/files/'. $file_name))
			{
				\dash\notif::ok(T_("File successfully deleted"));
				\dash\redirect::pwd();
				return;
			}
		}
		else
		{
			\dash\notif::ok(T_("Dont!"));
			return false;
		}
	}

	public function backup_now($_db_name = null)
	{
		if(\dash\db::backup_dump(['download' => false, 'db_name' => $_db_name]))
		{
			\dash\notif::ok(T_("Backup complete"));
		}
		\dash\redirect::pwd();
	}

	public function backup_schedule()
	{

		$array =
		[
			'auto_backup' => \dash\request::post('auto_backup') === 'on' ? true : false,
			'every'       => \dash\request::post('every'),
			'time'        => \dash\request::post('time'),
			'life_time'   => \dash\request::post('life_time'),
			'db_name'     => db_name,
		];

		$array = json_encode($array, JSON_UNESCAPED_UNICODE);

		$url    = database . 'backup';

		if(!\dash\file::exists($url))
		{
			\dash\file::makeDir($url, null, true);
		}

		$url .= '/schedule';
		\dash\file::write($url, $array);

		\dash\notif::ok(T_("Auto backup schedule saved"));

	}
}
?>
