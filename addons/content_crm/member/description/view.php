<?php
namespace content_crm\member\description;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\dash\data::page_title(T_('user description'));
		\dash\data::page_desc(T_('Allow to set and change description of user'));
		\dash\data::page_pictogram('file-text-o');
	}
}
?>