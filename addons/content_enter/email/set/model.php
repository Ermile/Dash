<?php
namespace content_enter\email\set;


class model
{

	public function post()
	{
		if(\dash\request::post('email'))
		{
			\dash\utility\enter::set_session('temp_email', \dash\request::post('email'));
		}
		else
		{
			\dash\notif::error(T_("No email was send"));
			return false;
		}

		// set session verify_from set
		\dash\utility\enter::set_session('verify_from', 'email_set');

		// send code whit email
		\dash\utility\enter::send_code_email();
	}
}
?>