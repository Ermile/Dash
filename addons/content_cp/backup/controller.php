<?php
namespace content_cp\backup;

class controller
{
	// save backup in this folder
	public static function backup_addr()
	{
		return root. '/public_html/files/backup/';
	}


	public static function routing()
	{
		$download = \dash\request::get('download');
		if($download)
		{
			$file = self::backup_addr(). $download;
			if(is_file($file))
			{
				\dash\file::download($file);
			}
			else
			{
				\dash\header::status(404, T_("File not found"));
			}
		}
	}
}
?>