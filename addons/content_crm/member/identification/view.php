<?php
namespace content_crm\member\identification;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\content_crm\member\main\view::static_var();

		\dash\data::page_title(T_('Member identification detail'));
		\dash\data::page_desc(T_('set personal and birth identification detail and some other id detail like passport and etc'));
		\dash\data::page_pictogram('user-5');

	}
}
?>