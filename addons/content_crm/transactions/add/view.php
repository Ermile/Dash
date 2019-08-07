<?php
namespace content_crm\transactions\add;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Add new transactions"));
		\dash\data::page_desc(T_("Add new transactions for every one"));
		\dash\data::badge_link(\dash\url::here(). '/transactions');
		\dash\data::badge_text(T_('Back to transactions list'));
	}
}
?>