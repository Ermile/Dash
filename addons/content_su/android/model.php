<?php
namespace content_su\android;

class model
{
	public static function post()
	{
		if(\dash\request::post('makeToken'))
		{
			\dash\app\android::token(true);
			\dash\notif::ok(T_("Android token revoked"));
			\dash\redirect::pwd();
		}
	}
}
?>