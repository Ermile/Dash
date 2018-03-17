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
		if(\lib\user::login())
		{
			$user_id = \lib\user::id();
			$list = \lib\db\sessions::get_active_sessions($user_id);
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
		if(!\lib\user::login())
		{
			return false;
		}

		if(\lib\request::post('type') === 'terminate' && \lib\request::post('id') && is_numeric(\lib\request::post('id')))
		{
			if(\lib\db\sessions::is_my_session(\lib\request::post('id'), \lib\user::id()))
			{
				\lib\db\sessions::terminate_id(\lib\request::post('id'));
				\lib\notif::true(T_("Session terminated"));
				return true;
			}
		}
	}
}
?>