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


		$args['sort']            = 'datecreated';
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
					$args['comments.status'] = ["IN", "('awaiting', 'answered')"];
					break;

				case 'awaiting':
					$args['comments.status'] = "awaiting";
					break;

				case 'close':
					$args['comments.status'] = "close";
					break;

				case 'deleted':
					$args['comments.status'] = "deleted";
					break;

				default:
				case 'all':
					$args['comments.status'] = ["NOT IN", "('deleted')"];

					break;
			}
		}

		$user = \dash\request::get('user');

		if($user)
		{
			$user = \dash\coding::decode($user);
			if($user && \dash\permission::check('supportTicketView'))
			{
				$args['comments.user_id'] = $user;
			}
		}


		\content_support\home\view::dataList($args);

		\content_support\view::sidebarDetail();

	}

}
?>