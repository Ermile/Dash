<?php
namespace content_crm\member\contact;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\dash\data::page_title(T_('Member contact'));
		\dash\data::page_desc(T_('Change mobile number of member and parents, email and tel of home'));
		\dash\data::page_pictogram('mobile');
	}
}
?>