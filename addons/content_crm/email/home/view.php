<?php
namespace content_crm\email\home;

class view
{
	public static function config()
	{
		\dash\permission::access('cpEmail');

		\dash\data::page_title(T_("SMS Dashboard"));
		\dash\data::page_desc(T_("Check your sms setting and balance and quick navigate to every where"));

		\dash\data::page_pictogram('envelope');

		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Dashboard'));
	}
}
?>