<?php
namespace content_enter\signup;

class controller
{
	public static function routing()
	{
		if(\dash\request::get('referer') && \dash\request::get('referer') != '')
		{
			$_SESSION['enter_referer'] = \dash\request::get('referer');
		}

		\dash\utility\hive::set(true);
	}
}
?>