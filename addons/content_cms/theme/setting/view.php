<?php
namespace content_cms\theme\setting;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Edit setting"));

		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back'));
		\dash\data::page_pictogram('list');
	}
}
?>