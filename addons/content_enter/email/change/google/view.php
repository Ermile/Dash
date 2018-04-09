<?php
namespace content_enter\email\change\google;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Change google mail'));
		\dash\data::page_desc(\dash\data::page_title());

		\dash\data::oldGoogle_mail(\dash\utility\enter::get_session('old_google_mail'));
		\dash\data::newGoogle_mail(\dash\utility\enter::get_session('new_google_mail'));
	}
}
?>