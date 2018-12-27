<?php
namespace content_crm\member\glance;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\dash\data::page_title(T_('Glance user'));
		\dash\data::page_desc(' ');
		\dash\data::page_pictogram('user');

	}
}
?>