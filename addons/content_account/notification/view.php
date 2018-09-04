<?php
namespace content_account\notification;

class view
{

	public static function config()
	{
		\dash\notification::send('verificationCode', \dash\user::id(), ['name' => null, 'title' => null]);

		\dash\data::appkey(\dash\utility\appkey::get_app_key(\dash\user::id()));
	}
}
?>