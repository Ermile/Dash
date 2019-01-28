<?php
namespace content_support\ticket\report;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("Ticket report"));
		\dash\data::page_desc(' ');

		\dash\data::page_pictogram('chart');
		\dash\data::badge_text(T_('Back to tickets list'));

	}
}
?>