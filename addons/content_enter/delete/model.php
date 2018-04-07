<?php
namespace addons\content_enter\delete;


class model extends \addons\content_enter\main\model
{

	/**
	 * Posts an enter.
	 * user try to delete her account
	 * save why posted and verify user account
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_delete($_args)
	{
		if(\dash\request::post('why'))
		{
			\dash\utility\enter::session_set('why', \dash\request::post('why'));
		}
		// save log the user try to delete account
		\dash\db\logs::set('enter:delete:try', \dash\user::id(), ['meta' => ['session' => $_SESSION, 'input' => \dash\request::post()]]);
		// set session verify_from signup
		\dash\utility\enter::session_set('verify_from', 'delete');

		\dash\utility\enter::go_to_verify();
	}
}
?>