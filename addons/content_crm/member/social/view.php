<?php
namespace content_crm\member\social;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\dash\data::page_title(T_('Member social netword detail'));
		\dash\data::page_desc(T_('set social network detail and some other detail'));
		\dash\data::page_pictogram('network');
	}
}
?>