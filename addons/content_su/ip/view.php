<?php
namespace content_su\ip;


class view
{
	public static function config()
	{

		$ipFiles = @glob(root .'public_html/files/ip/*');

		$ipFiles_files = [];
		$raw_file = [];
		if($ipFiles && is_array($ipFiles))
		{
			foreach ($ipFiles as $key => $value)
			{
				$raw_file[basename($value)] = explode("\n", file_get_contents($value));
				$ipFiles_files [] =
				[
					'name'    => basename($value),
					'time'    => filemtime($value),
					'size'    => round(filesize($value) / 1024 / 1024, 1),
					'date'    => date("Y-m-d H:i:s", filemtime($value)),
					'ago'     => \dash\utility\human::timing(date("Y-m-d H:i:s", filemtime($value))),
				];
			}
			$ipFiles_files = array_reverse($ipFiles_files);
			\dash\data::ipFiles($ipFiles_files);
		}
		\dash\data::rawFile($raw_file);


		if(defined('db_log_name'))
		{
			\dash\data::databaseLog(true);
		}

		$ip = $_SERVER['REMOTE_ADDR'];
		$details = json_decode(@file_get_contents("http://ipinfo.io/{$ip}/json"), true);
		\dash\data::ipDetail($details);
	}
}
?>