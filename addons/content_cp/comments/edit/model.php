<?php
namespace content_cp\comments\edit;

class model
{
	public static function post()
	{
		$status = \dash\request::post('status');

		if(!$status)
		{
			\dash\notif::error(T_("Invalid status"));
			return false;
		}

		$post_detail = \dash\app\comment::edit(['status' => $status], \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
