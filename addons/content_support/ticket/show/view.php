<?php
namespace content_support\ticket\show;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("View ticket No"));
		\dash\data::page_desc(T_("Check detail of your ticket."). ' '. T_("We try to answer to you as soon as posibble."));

		\dash\data::page_pictogram('comments-o');

		\dash\data::badge_text(T_('Back to tickets list'));
		\dash\data::badge_link(\dash\url::this().\dash\data::accessGet());


		$parent = \dash\request::get('id');
		if(!$parent || !is_numeric($parent))
		{
			\dash\header::status(404, T_("Invalid id"));
		}

		$main = \dash\app\ticket::get(\dash\request::get('id'));
		if(!$main || !isset($main['user_id']))
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		if(isset($main['parent']))
		{
			\dash\redirect::to(\dash\url::this(). '/show?id='. $main['parent']);
			return;
		}

		\dash\data::masterTicketDetail($main);

		$ticket_user_id = $main['user_id'];
		\dash\data::masterTicketUser($ticket_user_id);
		$ticket_user_id = \dash\coding::decode($ticket_user_id);
		if(!$ticket_user_id)
		{
			\dash\header::status(403, T_("Ticket not found"));
		}


		if(!\dash\permission::check('supportTicketView'))
		{
			if(intval($ticket_user_id) === intval(\dash\user::id()))
			{
				// no problem
			}
			else
			{
				\dash\header::status(403, T_("This is not your ticket!"));
			}
		}

		$args['sort']            = 'id';
		$args['order']           = 'desc';
		$args['comments.type']   = 'ticket';
		$args['comments.parent'] = $parent;
		$args['pagenation']      = false;
		$args['join_user']       = true;

		$dataTable = \dash\app\ticket::list(null, $args);
		$main = \dash\app::fix_avatar($main);
		array_push($dataTable, $main);
		$dataTable = array_reverse($dataTable);

		\dash\data::dataTable($dataTable);

		if(isset($dataTable[0]['id']))
		{
			\dash\data::page_title(\dash\data::page_title() . ' '. \dash\utility\human::fitNumber($dataTable[0]['id']) );
		}

		if(\dash\permission::supervisor())
		{
			$args =
			[
				'sort'  => 'visitors.id',
				'order' => 'desc',
				'limit' => 5,
				'pagenation' => false,
			];
			$url = \dash\url::this().'/show?id='. \dash\request::get('id');

			$args['urls.urlmd5'] = md5($url);
			$args['visitors.user_id'] = $ticket_user_id;

			$lastSeen = \dash\db\visitors::search(\dash\request::get('q'), $args);
			\dash\data::lastSeen($lastSeen);

		}

	}
}
?>