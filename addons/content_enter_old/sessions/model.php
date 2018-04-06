<?php
namespace addons\content_enter\sessions;


class model extends \addons\content_enter\main\model
{
	/**
	 * Gets the enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function sessions_list()
	{
		if(\dash\user::login())
		{
			$user_id = \dash\user::id();
			$list = \dash\db\sessions::get_active_sessions($user_id);
			return $list;
		}
	}


	/**
	 * Posts an sessions.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_sessions($_args)
	{
		if(!\dash\user::login())
		{
			return false;
		}

		if(\dash\request::post('type') === 'terminate' && \dash\request::post('id') && is_numeric(\dash\request::post('id')))
		{
			if(\dash\db\sessions::is_my_session(\dash\request::post('id'), \dash\user::id()))
			{
				\dash\db\sessions::terminate_id(\dash\request::post('id'));
				\dash\notif::ok(T_("Session terminated"));
				return true;
			}
		}
	}
}
?>