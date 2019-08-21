<?php
namespace content_cms\theme\home;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Theme"));

		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Back to dashboard'));

	}
}
?>