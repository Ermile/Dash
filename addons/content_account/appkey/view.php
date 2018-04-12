<?php
namespace content_account\appkey;

class view
{

	public static function config()
	{
		\dash\data::appkey(\dash\utility\appkey::get_app_key(\dash\user::id()));
	}
}
?>