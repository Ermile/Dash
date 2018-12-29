<?php
namespace content_crm\member\general;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\dash\data::page_title(T_('Edit user general detail'));
		\dash\data::page_desc(T_('you can edit detail of member'));
		\dash\data::page_pictogram('user');

		\content_crm\member\main\view::static_var();


	}
}
?>