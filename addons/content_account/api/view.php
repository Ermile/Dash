<?php
namespace content_account\api;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_('API key'));


		\dash\data::badge_link(\dash\url::here(). '/security');
		\dash\data::badge_text(T_('Back to Account security'));

		\dash\data::apikey(\dash\app\user_auth::get_apikey(\dash\user::id(), 'api'));
		\dash\data::myTitle(T_(':val API documentation', ['val' => \dash\data::site_title()]));
	}
}
?>