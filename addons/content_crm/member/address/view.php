<?php
namespace content_crm\member\address;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRow();

		\dash\data::page_title(T_('Member address'));
		\dash\data::page_desc(T_('set current location and full address'));
		\dash\data::page_pictogram('map-marker');

		\content_crm\member\main\view::static_var();
	}
}
?>