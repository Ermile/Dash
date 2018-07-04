<?php
namespace content_support\home;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("Ticketing System"));
		\dash\data::page_desc(T_("Easily manage your tickets and monitor or track them to get best answer until fix your problem"));

		$args['sort']            = 'id';
		$args['order']           = 'desc';
		$args['comments.type']   = 'ticket';

		if(!\dash\permission::check('supportTicketView'))
		{
			$args['user_id']         = \dash\user::id();
		}

		$args['comments.parent'] = null;
		$args['pagenation']      = false;
		$args['join_user']       = true;
		$args['comments.status']       = ["NOT IN", "('close')"];

		$dataTable = \dash\app\comment::list(null, $args);

		\dash\data::dataTable($dataTable);
	}
}
?>