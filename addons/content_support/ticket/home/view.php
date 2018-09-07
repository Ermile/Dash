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
					\dash\data::page_title(T_("Open tickets"));
					$args['comments.status'] = ["IN", "('awaiting', 'answered')"];
					break;

				case 'awaiting':
					\dash\data::page_title(T_("Tickets waiting for the answer"));
					$args['comments.status'] = "awaiting";
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

		$subdomain = \dash\request::get('subdomain');

		if($subdomain && \dash\permission::check('supportTicketView'))
		{
			$args['comments.subdomain'] = $subdomain;
		}


		$tag = \dash\request::get('tag');

		if($tag && \dash\permission::check('supportTicketView'))
		{
			$args['search_tag'] = $tag;
		}

		\content_support\view::dataList($args);
	}

}
?>