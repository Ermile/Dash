<?php
namespace content_crm\member\ticket;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		$args            = [];
		$args['user_id'] = \dash\coding::decode(\dash\request::get('id'));
		$args['type']    = 'ticket';
		if(\dash\request::get('type') === 'message')
		{

		}
		else
		{
			$args['parent']  = null;
		}

		$dataTable          = \dash\app\ticket::list(null, $args);

		\dash\data::dataTable($dataTable);

		\dash\data::myUrlAddress(\dash\url::this(). '/address');

		\dash\data::display_admin('content_crm/layout.html');

		\dash\data::page_title(T_('Member address'));
		\dash\data::page_desc(T_('set current location and full address'));
		\dash\data::page_pictogram('map-marker');

	}
}
?>