<?php
namespace content_su\backup;

class controller
{
	public static function routing()
	{

		$download = \dash\request::get('download');
		if($download)
		{
			\dash\file::download(database. 'backup/files/'. $download);
			return;
		}
	}
}
?>