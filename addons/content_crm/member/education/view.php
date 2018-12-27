<?php
namespace content_crm\member\education;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\dash\data::page_title(T_('Member education detail'));
		\dash\data::page_desc(T_('set education detail and some other detail'));
		\dash\data::page_pictogram('medal');
	}
}
?>