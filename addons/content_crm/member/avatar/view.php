<?php
namespace content_crm\member\avatar;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRow();

		\dash\data::page_title(T_('Member avatar'));
		\dash\data::page_desc(T_('Allow to set and change avatar of member'));
		\dash\data::page_pictogram('info-circle');
	}
}
?>