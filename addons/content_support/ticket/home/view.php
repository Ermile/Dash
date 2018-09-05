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
		\dash\data::badge_link(\dash\url::this(). '/add');

		\dash\data::badge2_text(T_('Back to support panel'));
		\dash\data::badge2_link(\dash\url::here());

		$args = [];

		\dash\data::haveSubdomain(true);
		$access = \dash\request::get('access');
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
				$args['user_id']         = \dash\user::id();
			}
		}
		else
		{
			if(!in_array($access, ['mine', 'all']))
			{
				\dash\header::status(404, T_("Invalid access in url"));
			}

			if($access === 'mine')
			{
				$args['user_id']         = \dash\user::id();
			}
			elseif($access === 'all')
			{
				\dash\data::haveSubdomain(true);
				\dash\permission::access('supportTicketViewAll');
			}
		}
		$args['user_id']         = \dash\user::id();
		$args['sort']            = 'datecreated';
		$args['order']           = 'desc';
		$args['comments.type']   = 'ticket';
		$args['comments.parent'] = null;
		$args['limit']           = 100;
		$args['join_user']       = true;
		$args['get_tag']         = true;
		$args['comments.status'] = ["NOT IN", "('close')"];

		$dataTable = \dash\app\ticket::list(null, $args);
		$dataTable = array_map(['\content_support\home\view', 'tagDetect'], $dataTable);

		\dash\data::dataTable($dataTable);

	}

}
?>