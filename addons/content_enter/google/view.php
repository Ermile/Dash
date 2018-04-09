<?php
namespace content_enter\google;


class view
{
	public static function config()
	{
		$auth_url = \dash\utility\google::auth_url();

		// auto redirect if url is clean
		if($auth_url && !\dash\request::get() && !\dash\request::post() && \dash\data::googleLogin())
		{
			\dash\redirect::to($auth_url);
		}

		\dash\data::page_title(T_('Enter to :name with google', ['name' => \dash\data::site_title()]));
		\dash\data::page_special(true);
		\dash\data::page_desc(\dash\data::page_title());
	}
}
?>