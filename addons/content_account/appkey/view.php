<?php
namespace content_account\appkey;

class view
{

	public static function config()
	{
		\dash\data::appkey(\dash\app\user_auth::get_appkey(\dash\user::id()));
		\dash\data::myTitle(T_(':val API documentation', ['val' => \dash\data::site_title()]));
	}
}
?>