<?php
namespace content_enter\verify\force;


class view
{
	public static function config()
	{
		\content_enter\verify\view::verifyPageTitle();

		if(\dash\utility\enter::get_session('verify_from') === 'ask_twostep' && \dash\option::config('force_enter_passcode'))
		{
			// nothing
		}
		else
		{
			\dash\header::status(404);
		}

	}
}
?>