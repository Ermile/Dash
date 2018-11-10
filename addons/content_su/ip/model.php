<?php
namespace content_su\ip;

class model
{
	public static function post()
	{
		if(\dash\request::post('type') === 'remove' && \dash\request::post('file'))
		{
			$file_name = \dash\request::post('file');
			if(\dash\file::delete(root .'public_html/files/ip/'. $file_name))
			{
				\dash\log::set('ipFileDeleted');
				\dash\notif::ok(T_("File successfully deleted"));
				\dash\redirect::pwd();
				return;
			}
		}
	}
}
?>
