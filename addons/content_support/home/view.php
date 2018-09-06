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
		\dash\data::badge_link(\dash\url::here(). '/ticket'. \dash\data::accessGet());

		$args['sort']            = 'datecreated';
		$args['order']           = 'desc';
		$args['comments.type']   = 'ticket';
		$args['comments.parent'] = null;
		$args['pagenation']      = false;
		$args['limit']           = 5;
		$args['join_user']       = true;
		$args['get_tag']         = true;
		$args['comments.status'] = ["NOT IN", "('close')"];

		self::dataList($args);

		\content_support\view::sidebarDetail(true);
	}


	public static function dataList($_args)
	{
		$args = $_args;

		\dash\data::haveSubdomain(true);

		switch (\dash\data::accessMode())
		{
			case 'mine':
				$args['user_id'] = \dash\user::id();
				break;

			case 'all':
				\dash\permission::access('supportTicketViewAll');
				break;

			case 'manage':
				\dash\permission::access('supportTicketView');

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

		$dataTable = \dash\app\ticket::list(null, $args);
		$dataTable = array_map(['self', 'tagDetect'], $dataTable);

		\dash\data::dataTable($dataTable);
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