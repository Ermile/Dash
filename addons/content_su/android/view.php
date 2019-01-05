<?php
namespace content_su\android;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Android application token"));
		\dash\data::page_desc(T_('Show android application token information'));
		\dash\data::page_pictogram('link');
		\dash\data::badge_text(T_('Back to dashboard'));
		\dash\data::badge_link(\dash\url::here());

		$load = \dash\app\android::load();

		\dash\data::androidDetail($load);
	}
}
?>