<?php
namespace content_account\personalization\sidebar;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Sidebar'));

		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back to personal info'));
	}
}
?>
