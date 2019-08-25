<?php
namespace content_support\ticket\show;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("View ticket No"));
		\dash\data::page_desc(T_("Check detail of your ticket."). ' '. T_("We try to answer to you as soon as posibble."));

		\dash\data::page_pictogram('tickets-o');

		\dash\data::badge_text(T_('Back to tickets list'));
		\dash\data::badge_link(\dash\url::this().\dash\data::accessGet());

		// set tg account if enable and we have bot
		if(\dash\option::social('telegram', 'status'))
		{
			if(\dash\option::social('telegram', 'token'))
			{
				if(\dash\option::social('telegram', 'hidden'))
				{
					// hide telegram
				}
				else
				{
					\dash\data::tgBot(\dash\option::social('telegram', 'bot'));
				}
			}
		}

		self::load_tichet(\dash\request::get('id'));

		$uaseTelegram = \dash\db\user_telegram::get(['user_id' => \dash\coding::decode(\dash\data::masterTicketDetail_user_id()), 'limit' => 1]);

		\dash\data::uaseTelegram($uaseTelegram);

	}

	public static function load_tichet($_id)
	{

		$parent = $_id;
		if(!$parent || !is_numeric($parent))
		{
			\dash\header::status(404, T_("Invalid id"));
		}

		$main = \dash\app\ticket::get($_id);
		if(!$main || !array_key_exists('user_id', $main))
		{
			\dash\header::status(404, T_("Ticket not found"));
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
			\dash\header::status(404, T_("Ticket not found"));
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
					\dash\redirect::to(\dash\url::kingdom(). '/enter?referer='. \dash\url::pwd());
					// \dash\header::status(403, T_("This is not your ticket!"));
				}
			}
		}

		$args['sort']            = 'id';
		$args['order']           = 'desc';
		if(\dash\permission::check('supportTicketAddNote'))
		{
			$args['tickets.type']   = ['IN', "('ticket', 'ticket_note')"];
		}
		else
		{
			$args['tickets.type']   = 'ticket';
		}

		$args['tickets.parent'] = $parent;
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
			$getTagArgs = ['type' => 'support_tag'];
			if(!\dash\option::config('no_subdomain'))
			{
				$subdomain = \dash\url::subdomain();
				if($subdomain)
				{
					$getTagArgs['subdomain'] = $subdomain;
				}
				else
				{
					$getTagArgs['subdomain'] = null;
				}
			}

			$all_tag = \dash\db\terms::get($getTagArgs);
			if(is_array($all_tag))
			{
				$all_tag = array_map(['\dash\app\term', 'ready'], $all_tag);
			}
			\dash\data::tagList($all_tag);
		}
		\content_support\ticket\home\view::sidebarDetail(true);

		self::see_ticket($main, $dataTable, $_id);
		self::inline_log($main, $dataTable, $_id);
	}

	public static function inline_log($_main, $_dataTable, $_id)
	{
		if(!\dash\permission::supervisor())
		{
			return;
		}

		$url = \dash\url::this(). '/show?id='. $_id;

		$implode_caller =
		[
			'ticket_ticketAddTag',
			'ticket_setCloseTicket',
			'ticket_setAwaitingTicket',
			'ticket_setDeleteTicket',
			'ticket_setSolvedTicket',
			'ticket_setUnSolvedTicket',
			// 'AddNoteTicket',
			// 'AddToTicket',
			// 'AnswerTicket',
		];

		$implode_caller = implode("','", $implode_caller);

		$get_log =
		[
			'caller'    => ['IN', "('$implode_caller')"],
			'code'      => $_id,

		];

		$get_log = \dash\db\logs::get($get_log, ['join_user' => true]);

		$date = [];
		foreach ($_dataTable as $key => $value)
		{
			if(isset($value['datecreated']))
			{
				if(!isset($date[$value['datecreated']]))
				{
					$date[$value['datecreated']] = [];
				}

				$date[$value['datecreated']][] = ['xtype' => 'ticket', 'value' => $value];
			}
		}

		foreach ($get_log as $key => $value)
		{
			if(isset($value['datecreated']))
			{
				if(!isset($date[$value['datecreated']]))
				{
					$date[$value['datecreated']] = [];
				}

				$value = \dash\app\log::ready($value);


				$date[$value['datecreated']][] = ['xtype' => 'log', 'value' => $value];
			}
		}

		ksort($date);
		\dash\data::superDataTable($date);

	}

	public static function see_ticket($_main, $_dataTable, $_id)
	{
		if(!self::is_my_ticket($_main) || !\dash\user::id() || !$_dataTable || !is_array($_dataTable))
		{
			return;
		}

		$end_message = end($_dataTable);


		if(!isset($end_message['user_id']) || !isset($end_message['type']) || !isset($end_message['datecreated']))
		{
			return;
		}

		$end_message['user_id'] = \dash\coding::decode($end_message['user_id']);

		if(intval($end_message['user_id']) === intval(\dash\user::id()))
		{
			return;
		}

		// this message seen before
		if(isset($end_message['see']) && $end_message['see'])
		{
			return;
		}

		\dash\db\tickets::update(['see' => 1], $end_message['id']);

		\dash\log::set('ticket_seeTicket',  ['code' => $_id, 'masterid' => $_id]);

		// $get_log =
		// [
		// 	'caller'      => 'ticket_seeTicket',
		// 	'code'        => $_id,
		// 	// 'to'       => \dash\user::id(),
		// 	'datecreated' => [">=", "'$end_message[datecreated]'"],
		// 	'limit'       => 1,
		// ];

		// $get_log = \dash\db\logs::get($get_log);

		// if(!$get_log)
		// {
		// 	\dash\log::set('ticket_seeTicket',  ['code' => $_id]);
		// }

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