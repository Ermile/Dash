<?php
namespace content_crm\transactions\minus;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Minus charge account"));

		\dash\data::badge_link(\dash\url::here(). '/transactions');
		\dash\data::badge_text(T_('Back to transactions list'));
	}
}
?>