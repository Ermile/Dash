<?php
namespace content_cms\theme\contents;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Edit contents"));

		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back'));
		\dash\data::page_pictogram('list');
	}
}
?>