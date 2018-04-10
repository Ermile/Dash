<?php
namespace content_enter\verify\telegram;


class view
{
	public static function config()
	{
		\content_enter\verify\view::verifyPageTitle();

		if(!\dash\utility\enter::get_session('run_telegram_to_user'))
		{
			\dash\utility\enter::set_session('run_telegram_to_user', true);
			\content_enter\verify\telegram\model::send_telegram_code();
		}
	}
}
?>