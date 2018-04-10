<?php
namespace content_enter\verify\call;


class view
{
	public static function config()
	{
		if(!\dash\utility\enter::get_session('run_call_to_user'))
		{
			\dash\utility\enter::set_session('run_call_to_user', true);
			\content_enter\verify\call\model::send_call_code();
		}
	}
}
?>
