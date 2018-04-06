<?php
namespace addons\content_enter\email\set;


class model extends \addons\content_enter\main\model
{

	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_email($_args)
	{
		if(\dash\request::post('email'))
		{
			\dash\utility\enter::session_set('temp_email', \dash\request::post('email'));
		}
		else
		{
			// plus count invalid emailword
			self::plus_try_session('no_email_send_set');

			\dash\notif::error(T_("No email was send"));
			return false;
		}

		// set session verify_from set
		\dash\utility\enter::session_set('verify_from', 'email_set');

		// send code whit email
		self::send_code_email();
	}
}
?>