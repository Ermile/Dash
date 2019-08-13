<?php
namespace content_support\message;

class view
{

	public static function config()
	{

		\dash\data::page_title(T_("Ticketing System"));
		\dash\data::page_desc(T_("Easily manage your message and monitor or track them to get best answer until fix your problem"));
		\dash\data::page_pictogram('life-ring');

		$args = [];

		$args['sort']            = 'datecreated';
		$args['order']           = 'desc';

		if(\dash\permission::check('supportTicketAddNote'))
		{
			$args['tickets.type']   = ["IN", "('ticket', 'ticket_note')"];
		}
		else
		{
			$args['tickets.type']   = 'ticket';
		}

		$args['limit']           = 50;
		$args['join_user']       = true;
		$args['get_tag']         = true;
		$args['tickets.status'] = ["NOT IN", "('deleted')"];

		\content_support\ticket\home\view::dataList($args);

		\dash\data::badge_text(T_('Tickets'));
		\dash\data::badge_link(\dash\url::here(). '/ticket'. \dash\data::accessGet());
	}



}
?>