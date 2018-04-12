<?php
namespace content_su\transactions\add;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Add new transactions"));
		\dash\data::page_desc(T_("Add new transactions for every one"));
		\dash\data::badge_link(\dash\url::this(). '/transactions');
		\dash\data::badge_text(T_('Back to transactions list'));
	}
}
?>