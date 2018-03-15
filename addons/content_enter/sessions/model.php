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
		if($this->login())
		{
			$user_id = $this->login('id');
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
		if(!$this->login())
		{
			return false;
		}

		if(\lib\utility::post('type') === 'terminate' && \lib\utility::post('id') && is_numeric(\lib\utility::post('id')))
		{
			if(\lib\db\sessions::is_my_session(\lib\utility::post('id'), $this->login('id')))
			{
				\lib\db\sessions::terminate_id(\lib\utility::post('id'));
				\lib\debug::true(T_("Session terminated"));
				return true;
			}
		}
	}
}
?>