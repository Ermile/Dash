<?php
namespace content_enter\delete;


class model
{
	public static function post()
	{
		if(\dash\request::post('why'))
		{
			\dash\utility\enter::set_session('why', \dash\request::post('why'));
		}
		// save log the user try to delete account
		\dash\db\logs::set('enter:delete:try', \dash\user::id(), ['meta' => ['session' => $_SESSION, 'input' => \dash\request::post()]]);
		// set session verify_from signup
		\dash\utility\enter::set_session('verify_from', 'delete');

		\dash\log::set('tryToDeleteAccount');

		\dash\utility\enter::set_session('usernameormobile', \dash\user::detail('mobile'));

		\dash\utility\enter::go_to_verify();
	}
}
?>