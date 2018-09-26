<?php
namespace content_su\time;

class view
{
	public static function config()
	{
		\dash\log::set('timeView');
		\dash\data::page_title(T_("Date and Time"));
		\dash\data::page_desc(T_('Check server date and time'));
		\dash\data::timeZone(date_default_timezone_get());
	}
}
?>