<?php
namespace content_support\home;

class view
{

	public static function config()
	{
		\dash\data::page_title(T_("Ticketing System"));
		\dash\data::page_desc(T_("Easily manage your tickets and monitor or track them to get best answer until fix your problem"));
		\dash\data::page_pictogram('life-ring');

		\dash\data::badge_text(T_('Tickets'));
		\dash\data::badge_link(\dash\url::here(). '/ticket');

		$args = [];

		$access = \dash\request::get('access');

		$access_mode = 'manage';

		if(!$access)
		{
			if(\dash\url::subdomain())
			{
				\dash\data::haveSubdomain(true);
				$args['comments.subdomain']    = \dash\url::subdomain();
			}
			else
			{
				$args['comments.subdomain']    = null;
			}

			if(!\dash\permission::check('supportTicketView'))
			{
				$access_mode = 'mine';
				$args['user_id']         = \dash\user::id();
			}
		}
		else
		{
			if(!in_array($access, ['mine', 'all', 'manage']))
			{
				\dash\header::status(404, T_("Invalid access in url"));
			}

			$access_mode = $access;

			if($access === 'mine')
			{
				$args['user_id']         = \dash\user::id();
			}
			elseif($access === 'all')
			{
				\dash\data::haveSubdomain(true);
				\dash\permission::access('supportTicketViewAll');
			}
			else
			{
				\dash\permission::access('supportTicketView');
			}
		}

		\dash\data::accessMode($access_mode);

		$args['sort']            = 'datecreated';
		$args['order']           = 'desc';
		$args['comments.type']   = 'ticket';
		$args['comments.parent'] = null;
		$args['pagenation']      = false;
		$args['limit']           = 5;
		$args['join_user']       = true;
		$args['get_tag']         = true;
		$args['comments.status'] = ["NOT IN", "('close')"];

		$dataTable = \dash\app\ticket::list(null, $args);
		$dataTable = array_map(['self', 'tagDetect'], $dataTable);

		\dash\data::dataTable($dataTable);

		// \dash\data::dashboardDetail(self::dashboardDetail());
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

	private static function dashboardDetail()
	{
		$args = [];
		$args['type'] = 'ticket';
		if(!\dash\permission::check('supportTicketView'))
		{
			$args['user_id'] = \dash\user::id();
		}
		if(\dash\url::subdomain())
		{
			$args['comments.subdomain']    = \dash\url::subdomain();
		}
		else
		{
			$args['comments.subdomain']    = null;
		}

		$result               = [];
		$args['parent']       = null;
		$result['tickets']    = \dash\db\comments::get_count($args);
		unset($args['parent']);
		$result['replies']    = \dash\db\comments::get_count($args);
		$args['status']       = 'close';
		$result['archived']   = \dash\db\comments::get_count($args);

		unset($args['status']);
		$args['parent']       = null;
		$result['avgfirst']   = \dash\db\comments::ticket_avg_first($args);
		$result['avgarchive'] = \dash\db\comments::ticket_avg_archive($args);

		return $result;
	}
}
?>