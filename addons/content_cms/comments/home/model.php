<?php
namespace content_cms\comments\home;

class model
{
	public static function post()
	{
		$status = \dash\request::post('status');
		$id     = \dash\request::post('id');

		if(!$status || !$id)
		{
			\dash\notif::warn(T_("Invalid detail"));
			return false;
		}

		$post_detail = \dash\app\comment::edit(['status' => $status], $id);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Comment successfully updated"));
			\dash\redirect::pwd();
		}
	}
}
?>
