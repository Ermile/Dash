<?php
namespace content_enter\verify\what;


class controller
{
	public static function routing()
	{
		if(\dash\utility\enter::lock('verify/what'))
		{
			\dash\header::status(404, 'verify/what');
		}

		if(!\dash\utility\enter::get_session('verify/what'))
		{
			\dash\utility\enter::set_session('verify/what', true);
			if(\dash\utility\enter::get_session('verification_code_id') && is_numeric(\dash\utility\enter::get_session('verification_code_id')))
			{
				\dash\log::set('verifyWhat');

				\dash\db\logs::update(['status' => 'expire'], \dash\utility\enter::get_session('verification_code_id'));
			}
		}
	}
}
?>