<?php
namespace content_account\appkey;


class model extends \content_account\main\model
{

	public function post_appkey()
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