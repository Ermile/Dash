<?php
namespace content_account\billing\detail;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Billing usage detail"));
		\dash\data::page_desc(T_("Check your current usage and active user and price for this period of time."));
		\dash\data::detail([]);
	}

}
?>