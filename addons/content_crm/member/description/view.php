<?php
namespace content_crm\member\description;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\dash\data::page_title(T_('User description'));
		\dash\data::page_desc(T_('Allow to set and change description of user'));
		\dash\data::page_pictogram('file-text-o');

		$args              = [];
		$args['user_id']   = \dash\coding::decode(\dash\request::get('id'));
		$args['subdomain'] = null;
		$dataTable         = \dash\db\userdetail::search(null, $args);

		\dash\data::dataTable($dataTable);
	}
}
?>