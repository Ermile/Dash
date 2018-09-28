<?php
namespace content_account\notifications;


class model
{
	public static function post()
	{
		if(!\dash\user::id())
		{
			return;
		}

		if(\dash\request::post('add') === 'appkey')
		{
			$check = \dash\utility\appkey::create_app_key(\dash\user::id());
			if($check)
			{

				\dash\notif::ok(T_("Creat new api key successfully complete"));
				\dash\redirect::pwd();
			}
			else
			{
				\dash\notif::error(T_("Error in create new api key"));
			}
		}
	}
}
?>