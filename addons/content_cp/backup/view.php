<?php
namespace content_cp\backup;


class view
{
	public static function config()
	{

		$oldBackup = @glob(root .'public_html/files/backup/*');

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

	}
}
?>