<?php
namespace content_su\apilog\view;


class view
{
	public static function config()
	{
		$myTitle = T_("View api log");


		\dash\data::page_title($myTitle);

		// add back level to summary link
		\dash\data::badge_text(T_('Back to log list'));
		\dash\data::badge_link(\dash\url::this());


		$load = \dash\db\apilog::get(['id' => \dash\request::get('id'), 'limit' => 1]);

		\dash\data::dataRow($load);

	}
}
?>