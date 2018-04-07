<?php
namespace content_enter\alert;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('Alert!'));
		\dash\data::page_special(true);
		\dash\data::page_desc(\dash\data::page_title());

		$alert = \dash\utility\enter::get_session('alert');

		\dash\data::alertMsg(T_("Alert!"). ' '. T_("What are you doing?"));
		if(isset($alert['text']))
		{
			\dash\data::alertMsg($alert['text']);
		}

		\dash\data::alertLink(\dash\url::here(). '/enter');
		if(isset($alert['link']))
		{
			\dash\data::alertLink($alert['link']);
		}

		\dash\data::alertButton(T_("Go back"));
		if(isset($alert['button']))
		{
			\dash\data::alertButton($alert['button']);
		}
	}
}
?>