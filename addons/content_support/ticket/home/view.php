<?php
namespace content_support\ticket\home;

class view
{


	public static function config()
	{

		\dash\data::page_title(T_("Tickets"));
		\dash\data::page_desc(T_("See list of your tickets!"));

		\dash\data::page_pictogram('question-circle');

		\dash\data::badge_text(T_('New ticket'));
		\dash\data::badge_link(\dash\url::this(). '/add'.\dash\data::accessGet());

		\dash\data::badge2_text(T_('Back to support panel'));
		\dash\data::badge2_link(\dash\url::here().\dash\data::accessGet());

		if(!\dash\user::login())
		{
			self::is_not_login();
		}
		else
		{
			self::is_login();
		}

	}

	private static function is_not_login()
	{
		if(isset($_SESSION['guest_ticket']) && is_array($_SESSION['guest_ticket']))
		{
			$guest_ticket_id = array_column($_SESSION['guest_ticket'], 'id');
			if($guest_ticket_id && is_array($guest_ticket_id))
			{
				$guest_ticket_id = implode(',', $guest_ticket_id);

				$args['sort']            = 'datecreated';
				$args['order']           = 'desc';
				$args['comments.type']   = 'ticket';
				$args['comments.id']     = ["IN", "($guest_ticket_id)"];

				$args['limit']           = 10;
				$args['join_user']       = true;
				$args['get_tag']         = true;
				$args['comments.status'] = ["NOT IN", "('deleted')"];

				self::dataList($args);

			}
		}
	}

	private static function is_login()
	{

		// load sidebar detail
		self::sidebarDetail(true);


		// 'approved','awaiting','unapproved','spam','deleted','filter','close','answered'
		// $args['order_raw']       = ' FIELD(comments.status, "answered", "awaiting") DESC, comments.status, IF(comments.datemodified is null, comments.datecreated, comments.datemodified) DESC';
		$args['sort']            = 'id';
		$args['order']           = 'desc';
		$args['comments.type']   = 'ticket';
		$args['comments.parent'] = null;

		$args['limit']           = 10;
		$args['join_user']       = true;
		$args['get_tag']         = true;


		$status = \dash\request::get('status');


		if($status)
		{
			switch ($status)
			{
				case 'open':
					\dash\data::page_title(T_("Open tickets"));
					$args['comments.status'] = ["IN", "('awaiting', 'answered')"];
					break;

				case 'awaiting':
					\dash\data::page_title(T_("Tickets waiting for the answer"));
					$args['comments.status'] = "awaiting";
					break;

				case 'unsolved':
					\dash\data::page_title(T_("Un solved ticket"));
					$args['1.1'] = ["= 1.1", " AND (comments.solved = b'0' OR comments.solved IS NULL ) "];
					break;

				case 'solved':
					\dash\data::page_title(T_("Solved ticket"));
					$args['comments.solved'] = 1;
					break;

				case 'answered':
					\dash\data::page_title(T_("Answered tickets"));
					$args['comments.status'] = "answered";
					break;

				case 'close':
				case 'archived':
					\dash\data::page_title(T_("Archived tickets"));
					$args['comments.status'] = "close";
					break;

				case 'deleted':
					\dash\data::page_title(T_("Deleted tickets"));
					$args['comments.status'] = "deleted";
					break;

				case 'all':
					\dash\data::page_title(T_("All tickets"));
					$args['comments.status'] = ["NOT IN", "('deleted')"];
					break;

				default:
					$args['order_raw']       = ' IF(comments.datemodified is null, comments.datecreated, comments.datemodified) DESC';
					unset($args['sort']);
					// $args['comments.status'] = ["NOT IN", "('deleted')"];
					break;
			}
		}
		else
		{
			$args['order_raw']       = ' IF(comments.datemodified is null, comments.datecreated, comments.datemodified) DESC';
			unset($args['sort']);
			$args['comments.status'] = ["NOT IN", "('deleted')"];
		}

		$user = \dash\request::get('user');

		if($user)
		{
			$user = \dash\coding::decode($user);
			if($user && \dash\permission::check('supportTicketManage'))
			{
				$args['comments.user_id'] = $user;
			}
		}

		$subdomain = \dash\request::get('subdomain');

		if($subdomain && \dash\permission::check('supportTicketManageSubdomain'))
		{
			$args['comments.subdomain'] = $subdomain;
		}


		$tag = \dash\request::get('tag');

		if($tag && \dash\permission::check('supportTicketManage'))
		{
			$args['search_tag'] = $tag;
		}

		$all_list       = self::dataList($args);

		$all_list = \dash\app\ticket::get_user_in_ticket($all_list);
		\dash\data::dataTable($all_list);

	}




	public static function sidebarDetail($_all = false)
	{
		if(!\dash\user::login())
		{
			return;
		}
		$args               = [];
		$args_tag           = [];

		$args['comments.type']   = 'ticket';
		$args['comments.parent'] = null;

		if(\dash\request::get('user'))
		{
			$user = \dash\coding::decode(\dash\request::get('user'));
			if($user && \dash\permission::check('supportTicketManage'))
			{
				$args['comments.user_id'] = $user;
			}
		}

		if(!\dash\data::haveSubdomain())
		{
			if(\dash\data::subdomain())
			{
				$args['comments.subdomain']    = \dash\url::subdomain();
			}
			else
			{
				$args['comments.subdomain']    = null;
			}
		}

		$result               = [];

		$args['comments.status'] = ["NOT IN ", "('deleted')"];
		if(\dash\data::accessMode() === 'mine')
		{
			$args['comments.user_id'] = \dash\user::id();
			$result['all']   = $result['mine']  = \dash\db\comments::get_count(array_merge($args,[]));
		}
		else
		{
			$result['all']      = \dash\db\comments::get_count(array_merge($args, []));
		}
		unset($args['comments.status']);

		$result['unsolved']   = \dash\db\comments::get_count(array_merge($args,['1.1' => ["= 1.1", " AND (comments.solved = b'0' OR comments.solved IS NULL ) "]]));

		$result['solved']   = \dash\db\comments::get_count(array_merge($args,['solved' => 1]));

		$result['answered'] = \dash\db\comments::get_count(array_merge($args,['status' => 'answered']));
		$result['awaiting'] = \dash\db\comments::get_count(array_merge($args, ['status' => 'awaiting']));
		$result['open']     = intval($result['answered']) + intval($result['awaiting']);

		$result['archived'] = \dash\db\comments::get_count(array_merge($args,['status' => 'close']));
		$result['trash']    = \dash\db\comments::get_count(array_merge($args,['status' => 'deleted']));

		$args_tag = $args;
		if($_all)
		{

			unset($args['comments.parent']);

			$result['message']       = \dash\db\comments::get_count($args);
			$args['comments.status'] = ["NOT IN ", "('deleted')"];
			$args['status']          = 'close';
			$args['comments.parent'] = null;
			$result['avgfirst']      = \dash\db\comments::ticket_avg_first($args);
			$result['avgarchive']    = \dash\db\comments::ticket_avg_archive($args);

		}

		$tags = \dash\db\comments::ticket_tag($args_tag);
		$result['tags'] = array_map(['\dash\app\term', 'ready'], $tags);


		\dash\data::sidebarDetail($result);
		return $result;

	}



	public static function dataList($_args)
	{
		$args = $_args;

		\dash\data::haveSubdomain(true);

		switch (\dash\data::accessMode())
		{
			case 'mine':
				$args['comments.user_id'] = \dash\user::id();
				break;

			case 'all':
				\dash\permission::access('supportTicketManageSubdomain');
				break;

			case 'manage':
				\dash\permission::access('supportTicketManage');

				\dash\data::haveSubdomain(false);

				if(\dash\url::subdomain())
				{
					$args['comments.subdomain']    = \dash\url::subdomain();
				}
				else
				{
					$args['comments.subdomain']    = null;
				}
				break;

			default:
				break;
		}

		$dataTable = \dash\app\ticket::list(\dash\request::get('q'), $args);
		$dataTable = array_map(['self', 'tagDetect'], $dataTable);

		\dash\data::dataTable($dataTable);

		return $dataTable;
	}


	public static function tagDetect($_data)
	{
		if(isset($_data['tag']))
		{
			$tag = $_data['tag'];
			$tag = explode(',', $tag);
			$_data['tag'] = $tag;
		}
		return $_data;
	}

}
?>