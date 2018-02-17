<?php
namespace addons\content_su\backup;

class model extends \addons\content_su\main\model
{
	public function post_backup()
	{
		if(\lib\utility::post('backup') === 'now')
		{
			$this->backup_now();
		}
		elseif(\lib\utility::post('backup') === 'schedule')
		{
			$this->backup_schedule();
		}
		elseif(\lib\utility::post('type') === 'remove' && \lib\utility::post('file'))
		{
			$file_name = \lib\utility::post('file');
			if(\lib\utility\file::delete(database. 'backup/files/'. $file_name))
			{
				\lib\debug::true(T_("File successfully deleted"));
				$this->redirector($this->url('full'));
				return;
			}
		}
		else
		{
			\lib\debug::true(T_("Dont!"));
			return false;
		}
	}

	public function backup_now()
	{
		if(\lib\db::backup_dump(['download' => false]))
		{
			\lib\debug::true(T_("Backup complete"));
		}
		$this->redirector($this->url('full'));
	}

	public function backup_schedule()
	{

		$array = 
		[
			'auto_backup' => \lib\utility::post('auto_backup') === 'on' ? true : false,
			'every'       => \lib\utility::post('every'),
			'time'        => \lib\utility::post('time'),
			'life_time'   => \lib\utility::post('life_time'),
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
