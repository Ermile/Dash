<?php
namespace content_enter\verify\force;


class model
{

	public static function post()
	{
		if(\dash\utility\enter::get_session('verify_from') === 'ask_twostep' && \dash\option::config('force_enter_passcode'))
		{
			$code = \dash\request::post('code');

			if($code === \dash\option::config('force_enter_passcode'))
			{
				// expire code
				if(\dash\utility\enter::get_session('verification_code_id'))
				{
					// the user enter the code and the code is ok
					// must expire this code
					\dash\db\logs::update(['status' => 'expire'], \dash\utility\enter::get_session('verification_code_id'));
					\dash\utility\enter::set_session('verification_code', null);
					\dash\utility\enter::set_session('verification_code_time', null);
					\dash\utility\enter::set_session('verification_code_way', null);
					\dash\utility\enter::set_session('verification_code_id', null);
				}

				// to no check again
				\dash\utility\enter::set_session('twostep_is_ok', true);
				// set login session
				$redirect_url = \dash\utility\enter::enter_set_login();

				// save redirect url in session to get from okay page
				\dash\utility\enter::set_session('redirect_url', $redirect_url);
				// set okay as next step
				\dash\utility\enter::next_step('okay');
				// go to okay page
				\dash\utility\enter::go_to('okay');
			}
			else
			{
				\dash\utility\enter::try('enter_invalid_force_code');
				\dash\redirect::pwd();
			}

		}
		else
		{
			\dash\header::status(404);
		}

	}


}
?>
