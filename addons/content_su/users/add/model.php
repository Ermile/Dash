<?php
namespace content_su\users\add;


class model
{
	public static function post()
	{
		$request                = [];
		$request['mobile']      = \dash\request::post('mobile');
		$request['displayname'] = \dash\request::post('displayname');

		\dash\app\user::add($request);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("User added"));
			\dash\redirect::to(\dash\url::this());
		}
	}
}
?>
