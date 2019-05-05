<?php
namespace content_cms\backup;


class view
{
	public static function config()
	{
		\dash\data::page_pictogram('database');

		$oldBackup = @glob(root .'public_html/files/backup/*');

		$oldBackup_files = [];

		if($oldBackup && is_array($oldBackup))
		{
			foreach ($oldBackup as $key => $value)
			{
				$zip = false;
				if(substr(basename($value), 0, 7) === 'Backup_')
				{
					$zip = true;
				}
				$oldBackup_files [] =
				[
					'zip'  => $zip,
					'name' => basename($value),
					'time' => filemtime($value),
					'size' => round(filesize($value) / 1024 / 1024, 1),
					'date' => date("Y-m-d H:i:s", filemtime($value)),
					'ago'  => \dash\utility\human::timing(date("Y-m-d H:i:s", filemtime($value))),
				];
			}
			\dash\data::oldBackup($oldBackup_files);
		}

	}
}
?>