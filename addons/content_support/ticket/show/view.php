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
		if(!$main || !array_key_exists('user_id', $main))
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		if(isset($main['parent']))
		{
			\dash\redirect::to(\dash\url::this(). '/show?id='. $main['parent']);
			return;
		}

		\dash\data::masterTicketDetail($main);

		if(isset($main['solved']) && isset($main['datemodified']))
		{
			$closeDate = $main['datemodified'];
			// add 24 hour to get close date
			$closeDate = date("Y-m-d H:i:s", strtotime('+24 hour', strtotime($closeDate)));

			$solvedMsg = T_("This ticket closed automatically on :time because it marked as solved.", ['time' => "<b>". \dash\datetime::fit($closeDate, 'shortTime'). "</b>"]). ' '. T_("If it's okay you can close it manually.");

			\dash\data::solvedMsg($solvedMsg);
		}

		$ticket_user_id = $main['user_id'];
		\dash\data::masterTicketUser($ticket_user_id);
		$ticket_user_id = \dash\coding::decode($ticket_user_id);

		if(!$ticket_user_id && !\dash\temp::get('ticketGuest') && !\dash\user::login())
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		if(!\dash\permission::check('supportTicketManage'))
		{
			if(intval($ticket_user_id) === intval(\dash\user::id()))
			{
				// no problem
			}
			else
			{
				if(!\dash\temp::get('ticketGuest'))
				{
					\dash\header::status(403, T_("This is not your ticket!"));
				}
			}
		}

		$args['sort']            = 'id';
		$args['order']           = 'desc';
		if(\dash\permission::check('supportTicketAddNote'))
		{
			$args['comments.type']   = ['IN', "('ticket', 'ticket_note')"];
		}
		else
		{
			$args['comments.type']   = 'ticket';
		}

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
			\dash\data::page_title(\dash\data::page_title() . ' '. \dash\utility\human::fitNumber($dataTable[0]['id'], false) );
		}

		\dash\data::isMyTicket(self::is_my_ticket($main));

		if(\dash\permission::supervisor())
		{
			$all_tag = \dash\db\terms::get(['type' => 'support_tag']);
			if(is_array($all_tag))
			{
				$all_tag = array_map(['\dash\app\term', 'ready'], $all_tag);
			}
			\dash\data::tagList($all_tag);
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
		\content_support\ticket\home\view::acceessModeDetector();
		\content_support\ticket\home\view::sidebarDetail(true);


	}


	public static function is_my_ticket($_main)
	{
		$main = $_main;

		if(!$main || !array_key_exists('user_id', $main))
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		$ticket_user_id = $main['user_id'];
		$ticket_user_id = \dash\coding::decode($ticket_user_id);
		if(!$ticket_user_id && !\dash\temp::get('ticketGuest') && !\dash\user::login())
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		$is_my_ticket = false;
		if($ticket_user_id && \dash\user::login() && intval($ticket_user_id) === intval(\dash\user::id()))
		{
			$is_my_ticket = true;
		}
		elseif(!\dash\user::login() && \dash\temp::get('ticketGuest'))
		{
			$is_my_ticket = true;
		}
		return $is_my_ticket;
	}


}
?>