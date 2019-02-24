<?php
namespace content_account\api;

class view
{

	public static function config()
	{
		\dash\data::apikey(\dash\app\user_auth::get_apikey(\dash\user::id(), 'api'));
	}
}
?>