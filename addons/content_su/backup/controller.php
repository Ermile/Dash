<?php
namespace content_su\backup;

class controller
{
	public static function routing()
	{

		$download = \dash\request::get('download');
		if($download)
		{
			\dash\log::db('downloadBackup');
			\dash\file::download(database. 'backup/files/'. $download);
			return;
		}
	}
}
?>