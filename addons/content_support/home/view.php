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


		$args['sort']            = 'id';
		$args['order']           = 'desc';
		$args['comments.type']   = 'ticket';

		if(!\dash\permission::check('supportTicketView'))
		{
			$args['user_id']         = \dash\user::id();
		}

		$args['comments.parent'] = null;
		$args['limit']      = 100;
		$args['join_user']       = true;
		$args['get_tag']         = true;
		$args['comments.status']       = ["NOT IN", "('close')"];
		if(\dash\url::subdomain())
		{
			$args['comments.subdomain']    = \dash\url::subdomain();
		}
		else
		{
			$args['comments.subdomain']    = null;
		}

		$dataTable = \dash\app\comment::list(null, $args);
		$dataTable = array_map(['self', 'tagDetect'], $dataTable);

		\dash\data::dataTable($dataTable);

		\dash\data::dashboardDetail(self::dashboardDetail());
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