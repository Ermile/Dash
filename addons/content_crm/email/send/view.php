<?php
namespace content_crm\email\send;

class view
{
	public static function config()
	{
		\dash\permission::access('cpEmailSend');

		\dash\data::page_title(T_("Send email to user"));
		\dash\data::page_desc(T_("Send every email to every user"));

		\dash\data::page_pictogram('envelope');

		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Dashboard'));
	}
}
?>