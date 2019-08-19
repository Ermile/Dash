<?php
namespace content_account\security\sessions;


class model
{

	public static function post()
	{

		if(\dash\request::post('type') === 'terminateall')
		{
			\dash\db\sessions::terminate_all_other(\dash\user::id());
			\dash\log::set('sessionTerminateAllOther');
			\dash\notif::ok(T_("All other session terminated"));
			\dash\redirect::pwd();
			return true;
		}

		if(\dash\request::post('type') === 'terminate' && \dash\request::post('id') && is_numeric(\dash\request::post('id')))
		{
			if(\dash\db\sessions::is_my_session(\dash\request::post('id'), \dash\user::id()))
			{
				\dash\log::set('sessionTerminate');
				\dash\db\sessions::terminate_id(\dash\request::post('id'));
				\dash\notif::ok(T_("Session terminated"));
				\dash\redirect::pwd();
				return true;
			}
		}
	}
}
?>