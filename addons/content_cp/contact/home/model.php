<?php
namespace content_cp\contact\home;

class model
{
	public static function post()
	{
		$action = \dash\request::post('action');
		$id     = \dash\request::post('id');

		if(!$action || !$id)
		{
			\dash\notif::warn(T_("Invalid detail"));
			return false;
		}

		$post_detail = \dash\app\comment::edit(['status' => $action], $id);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Comment successfully updated"));
			\dash\redirect::pwd();
		}
	}
}
?>
