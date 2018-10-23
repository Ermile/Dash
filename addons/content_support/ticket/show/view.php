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

		self::load_tichet(\dash\request::get('id'));
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
			$url = \dash\url::this().'/show?id='. $_id;

			$args['urls.urlmd5'] = md5($url);
			$args['visitors.user_id'] = $ticket_user_id;

			$lastSeen = \dash\db\visitors::search(\dash\request::get('q'), $args);
			\dash\data::lastSeen($lastSeen);

		}
		\content_support\ticket\home\view::sidebarDetail(true);

		self::see_ticket($main, $dataTable);
		self::inline_log($main, $dataTable);
	}

	public static function inline_log($_main, $_dataTable)
	{
		if(!\dash\permission::supervisor())
		{
			return;
		}

		$url = \dash\url::this(). '/show?id='. $_id;

		$get_visitor =
		[
			// some where
		];

		$get_visitor = \dash\db\visitors::get_url_like("$url%", $get_visitor);

		$implode_caller =
		[
			'ticketAddTag',
			'setCloseTicket',
			'setAwaitingTicket',
			'setDeleteTicket',
			'setSolvedTicket',
			'setUnSolvedTicket',
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

		foreach ($get_visitor as $key => $value)
		{
			if(isset($value['date']))
			{
				if(!isset($date[$value['date']]))
				{
					$date[$value['date']] = [];
				}

				$date[$value['date']][] = ['xtype' => 'visitor', 'value' => $value];
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

	public static function see_ticket($_main, $_dataTable)
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

		$get_log =
		[
			'caller'      => 'seeTicket',
			'code'        => $_id,
			'user_id'     => \dash\user::id(),
			'datecreated' => [">=", "'$end_message[datecreated]'"],
		];

		$get_log = \dash\db\logs::get($get_log);

		if(!$get_log)
		{
			\dash\log::set('seeTicket',  ['code' => $_id]);
		}

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