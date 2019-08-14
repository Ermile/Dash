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
				$args['tickets.type']   = 'ticket';
				$args['tickets.id']     = ["IN", "($guest_ticket_id)"];

				$args['limit']           = 10;
				$args['join_user']       = true;
				$args['get_tag']         = true;
				$args['tickets.status'] = ["NOT IN", "('deleted')"];

				self::dataList($args);

			}
		}
	}

	private static function is_login()
	{

		// load sidebar detail
		self::sidebarDetail(true);


		// 'approved','awaiting','unapproved','spam','deleted','filter','close','answered'
		// $args['order_raw']       = ' FIELD(tickets.status, "answered", "awaiting") DESC, tickets.status, IF(tickets.datemodified is null, tickets.datecreated, tickets.datemodified) DESC';
		$args['sort']            = 'id';
		$args['order']           = 'desc';
		$args['tickets.type']   = 'ticket';
		$args['tickets.parent'] = null;

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
					$args['tickets.status'] = ["IN", "('awaiting', 'answered')"];
					break;

				case 'awaiting':
					\dash\data::page_title(T_("Tickets waiting for the answer"));
					$args['tickets.status'] = "awaiting";
					break;

				case 'unsolved':
					\dash\data::page_title(T_("Un solved ticket"));
					$args['1.1'] = ["= 1.1", " AND (tickets.solved = b'0' OR tickets.solved IS NULL ) "];
					$args['tickets.status'] = ["NOT IN", "('deleted', 'spam')"];
					break;

				case 'solved':
					\dash\data::page_title(T_("Solved ticket"));
					$args['tickets.solved'] = 1;
					$args['tickets.status'] = ["NOT IN", "('deleted', 'spam')"];
					break;

				case 'answered':
					\dash\data::page_title(T_("Answered tickets"));
					$args['tickets.status'] = "answered";
					break;

				case 'spam':
					\dash\data::page_title(T_("Spam tickets"));
					$args['tickets.status'] = "spam";
					break;

				case 'close':
				case 'archived':
					\dash\data::page_title(T_("Archived tickets"));
					$args['tickets.status'] = "close";
					break;

				case 'deleted':
					\dash\data::page_title(T_("Deleted tickets"));
					$args['tickets.status'] = "deleted";
					break;

				case 'all':
					\dash\data::page_title(T_("All tickets"));
					$args['tickets.status'] = ["NOT IN", "('deleted', 'spam')"];
					break;

				default:
					// $args['order_raw']       = ' IF(tickets.datemodified is null, tickets.datecreated, tickets.datemodified) DESC';
					$args['order_raw']       = ' tickets.datecreated DESC';
					unset($args['sort']);
					// $args['tickets.status'] = ["NOT IN", "('deleted')"];
					break;
			}
		}
		else
		{
			// $args['order_raw']       = ' IF(tickets.datemodified is null, tickets.datecreated, tickets.datemodified) DESC';
			$args['order_raw']       = ' tickets.datecreated DESC';
			unset($args['sort']);
			$args['tickets.status'] = ["NOT IN", "('deleted', 'spam')"];
		}

		$user = \dash\request::get('user');

		if($user)
		{
			$user = \dash\coding::decode($user);
			if($user && \dash\permission::check('supportTicketManage'))
			{
				$args['tickets.user_id'] = $user;
			}
		}



		$subdomain = \dash\request::get('subdomain');

		if(!\dash\option::config('no_subdomain'))
		{
			if($subdomain && \dash\permission::check('supportTicketManageSubdomain'))
			{
				$args['tickets.subdomain'] = $subdomain;
			}
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

		$args['tickets.type']   = 'ticket';
		$args['tickets.parent'] = null;

		if(\dash\request::get('user'))
		{
			$user = \dash\coding::decode(\dash\request::get('user'));
			if($user && \dash\permission::check('supportTicketManage'))
			{
				$args['tickets.user_id'] = $user;
			}
		}

		if(!\dash\option::config('no_subdomain'))
		{
			if(!\dash\data::haveSubdomain())
			{
				if(\dash\data::subdomain())
				{
					$args['tickets.subdomain']    = \dash\url::subdomain();
				}
				else
				{
					$args['tickets.subdomain']    = null;
				}
			}
		}


		if(\dash\data::accessMode() === 'all')
		{
			unset($args['tickets.subdomain']);
		}

		$result               = [];

		$cach_key = json_encode($args). '_';
		$cach_key .= \dash\data::accessMode();
		$cach_key = md5($cach_key);
		$get_cach = \dash\session::get($cach_key, 'support_sidebar');

		if($get_cach)
		{
			\dash\data::sidebarDetail($get_cach);
			return $get_cach;
		}

		$args['tickets.status'] = ["NOT IN ", "('deleted' ,'spam')"];
		if(\dash\data::accessMode() === 'mine')
		{
			$args['tickets.user_id'] = \dash\user::id();
			$result['all']   = $result['mine']  = \dash\db\tickets::get_count(array_merge($args,[]));
		}
		else
		{
			$result['all']      = \dash\db\tickets::get_count(array_merge($args, []));
		}
		// unset($args['tickets.status']);

		$result['unsolved']   = \dash\db\tickets::get_count(array_merge($args,['1.1' => ["= 1.1", " AND (tickets.solved = b'0' OR tickets.solved IS NULL ) "]]));

		$result['solved']   = \dash\db\tickets::get_count(array_merge($args,['solved' => 1]));

		$result['answered'] = \dash\db\tickets::get_count(array_merge($args,['tickets.status' => 'answered']));
		$result['awaiting'] = \dash\db\tickets::get_count(array_merge($args, ['tickets.status' => 'awaiting']));
		$result['open']     = intval($result['answered']) + intval($result['awaiting']);

		$result['archived'] = \dash\db\tickets::get_count(array_merge($args,['tickets.status' => 'close']));
		$result['trash']    = \dash\db\tickets::get_count(array_merge($args,['tickets.status' => 'deleted']));
		$result['spam']    = \dash\db\tickets::get_count(array_merge($args,['tickets.status' => 'spam']));

		$args_tag = $args;
		if($_all)
		{

			unset($args['tickets.parent']);

			$result['message']       = \dash\db\tickets::get_count($args);
			$args['tickets.status'] = ["NOT IN ", "('deleted', 'spam')"];
			$args['tickets.status'] = 'close';
			$args['tickets.parent'] = null;
			$result['avgfirst']      = \dash\db\tickets::ticket_avg_first($args);
			$result['avgarchive']    = \dash\db\tickets::ticket_avg_archive($args);

		}

		$tags = \dash\db\tickets::ticket_tag($args_tag);
		$result['tags'] = array_map(['\dash\app\term', 'ready'], $tags);

		// remove all old session sidebar to create new
		\dash\session::clean_cat('support_sidebar');

		\dash\session::set($cach_key, $result, 'support_sidebar', (60 * 2));

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
				$args['tickets.user_id'] = \dash\user::id();
				break;

			case 'all':
				\dash\permission::access('supportTicketManageSubdomain');
				break;

			case 'manage':
				\dash\permission::access('supportTicketManage');

				if(!\dash\option::config('no_subdomain'))
				{
					\dash\data::haveSubdomain(false);

					if(\dash\url::subdomain())
					{
						$args['tickets.subdomain']    = \dash\url::subdomain();
					}
					else
					{
						$args['tickets.subdomain']    = null;
					}
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