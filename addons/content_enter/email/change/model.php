<?php
namespace content_enter\email\change;


class model
{

	public static function remove_email()
	{
		if(\dash\user::login('email') && \dash\user::id())
		{
			\dash\log::set('removeEmail');

			\dash\db\users::update(['email' => null], \dash\user::id());
			// set the alert message
			\dash\utility\enter::set_session('alert', ['text' => T_("Your email was removed")]);
			// open lock of alert page
			\dash\utility\enter::next_step('alert');
			// go to alert page
			\dash\utility\enter::go_to('alert');
		}
	}


	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post()
	{
		if(\dash\request::post('type') === 'remove')
		{
			self::remove_email();
			return;
		}

		if(!\dash\request::post('emailNew'))
		{
			\dash\notif::error(T_("Plese fill the new email"));
			return false;
		}

		if(\dash\user::login('email') == \dash\request::post('emailNew'))
		{
			\dash\notif::error(T_("Please select a different email"));
			return false;
		}

		if(\dash\request::post('emailNew'))
		{
			\dash\utility\enter::set_session('temp_email', \dash\request::post('emailNew'));
		}

		\dash\log::set('setNewEmail');

		// set session verify_from set
		\dash\utility\enter::set_session('verify_from', 'email_set');

		// send code whit email
		\dash\utility\enter::send_code_email();
	}
}
?>