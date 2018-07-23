<?php
namespace content_account\view;


class model
{
	public static function post()
	{
		if(!\dash\user::id())
		{
			return;
		}

		$togglesidebar = \dash\request::post('togglesidebar') ? 1 : 0;

		\dash\db\users::update(['sidebar' => $togglesidebar], \dash\user::id());

		\dash\user::refresh();

		\dash\notif::ok(T_("Your change was saved"));

		\dash\notif::direct();

		\dash\redirect::pwd();
	}
}
?>