<?php
namespace content_support\ticket\show;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("Add new ticket"));
		\dash\data::page_desc(T_("Dot worry!"). ' '. T_("Ask your question."). ' '. T_("We are here to answer your questions."));

		\dash\data::page_pictogram('comments');

		\dash\data::badge_text(T_('Back to tickets list'));
		\dash\data::badge_link(\dash\url::this());


		$parent = \dash\request::get('id');
		$parent = \dash\coding::decode($parent);

		if(!$parent)
		{
			\dash\header::status(404, T_("Invalid id"));
		}

		$main = \dash\app\comment::get(\dash\request::get('id'));
		if(!$main || !isset($main['user_id']))
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

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

		$dataTable = \dash\app\comment::list(null, $args);

		array_push($dataTable, $main);

		\dash\data::dataTable($dataTable);
	}
}
?>