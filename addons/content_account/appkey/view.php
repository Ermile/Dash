<?php
namespace content_account\appkey;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Application key'));


		\dash\data::badge_link(\dash\url::here(). '/security');
		\dash\data::badge_text(T_('Back to Account security'));

		\dash\data::appkey(\dash\app\user_auth::get_appkey(\dash\user::id()));
		\dash\data::myTitle(T_(':val API documentation', ['val' => \dash\data::site_title()]));
	}
}
?>