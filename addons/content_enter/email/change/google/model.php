<?php
namespace content_enter\email\change\google;


class model
{

	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post()
	{
		if(\dash\request::post('update') === 'no')
		{
			\dash\utility\enter::set_session('alert', [ 'text' => T_("Please log in with your previous email or ignore your mobile registration.")]);
			\dash\utility\enter::next_step('alert');
			\dash\utility\enter::go_to('alert');
			return;
		}

		$old_google_mail = \dash\utility\enter::get_session('old_google_mail');
		$new_google_mail = \dash\utility\enter::get_session('new_google_mail');

		$user_id = \dash\utility\enter::get_session('user_id_must_change_google_mail');
		if($old_google_mail && $new_google_mail && is_numeric($user_id))
		{

			\dash\db\users::update(['googlemail' => $new_google_mail], $user_id);
			\dash\utility\enter::load_user_data($user_id, 'user_id');
			\dash\utility\enter::enter_set_login();
			\dash\utility\enter::next_step('okay');
			\dash\utility\enter::go_to('okay');
		}

	}
}
?>