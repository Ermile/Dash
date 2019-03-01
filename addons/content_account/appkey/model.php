<?php
namespace content_account\appkey;


class model
{
	public static function post()
	{
		if(!\dash\user::id())
		{
			return;
		}

		if(!\dash\user::detail('verifymobile'))
		{
			\dash\notif::error(T_("You must verify your mobile to make an appkey"));
			return false;
		}

		if(\dash\request::post('add') === 'appkey')
		{
			$check = \dash\app\user_auth::make_appkey(\dash\user::id());
			if($check)
			{
				\dash\log::set('createNewAppkey');
				\dash\notif::ok(T_("Creat appkey successfully complete"));
				\dash\redirect::pwd();
			}
			else
			{
				\dash\notif::error(T_("Error in create new api key"));
			}
		}
		elseif(\dash\request::post('remove') === 'appkey')
		{
			\dash\notif::error(T_("You can not remove your appkey"));
			return;
		}
	}
}
?>