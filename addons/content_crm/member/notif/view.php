<?php
namespace content_crm\member\notif;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\dash\data::page_title(T_('Notification'));
		\dash\data::page_desc(T_('Allow to set notification to user'));
		\dash\data::page_pictogram('bullhorn');


	}
}
?>