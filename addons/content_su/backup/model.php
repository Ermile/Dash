<?php
namespace addons\content_su\backup;

class model extends \addons\content_su\main\model
{
	public function post_backup()
	{
		if(\lib\request::post('backup') === 'now')
		{
			$this->backup_now();
		}
		elseif(\lib\request::post('backup') === 'now_log')
		{
			if(defined('db_log_name'))
			{
				$this->backup_now(db_log_name);
			}
			else
			{
				\lib\debug::error(T_("Database of logs dose not exists"));
				return false;
			}
		}
		elseif(\lib\request::post('backup') === 'schedule')
		{
			$this->backup_schedule();
		}
		elseif(\lib\request::post('type') === 'remove' && \lib\request::post('file'))
		{
			$file_name = \lib\request::post('file');
			if(\lib\utility\file::delete(database. 'backup/files/'. $file_name))
			{
				\lib\debug::true(T_("File successfully deleted"));
				$this->redirector(\lib\url::pwd());
				return;
			}
		}
		else
		{
			\lib\debug::true(T_("Dont!"));
			return false;
		}
	}

	public function backup_now($_db_name = null)
	{
		if(\lib\db::backup_dump(['download' => false, 'db_name' => $_db_name]))
		{
			\lib\debug::true(T_("Backup complete"));
		}
		$this->redirector(\lib\url::pwd());
	}

	public function backup_schedule()
	{

		$array =
		[
			'auto_backup' => \lib\request::post('auto_backup') === 'on' ? true : false,
			'every'       => \lib\request::post('every'),
			'time'        => \lib\request::post('time'),
			'life_time'   => \lib\request::post('life_time'),
			'db_name'     => db_name,
		];

		$array = json_encode($array, JSON_UNESCAPED_UNICODE);

		$url    = database . 'backup';

		if(!\lib\utility\file::exists($url))
		{
			\lib\utility\file::makeDir($url, null, true);
		}

		$url .= '/schedule';
		\lib\utility\file::write($url, $array);

		\lib\debug::true(T_("Auto backup schedule saved"));

	}
}
?>
