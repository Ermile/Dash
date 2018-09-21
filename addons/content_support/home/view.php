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

		self::helpDashboard();
		// // 'approved','awaiting','unapproved','spam','deleted','filter','close','answered'
		// // $args['order_raw']       = ' FIELD(comments.status, "answered", "awaiting") DESC, comments.status, IF(comments.datemodified is null, comments.datecreated, comments.datemodified) DESC';
		// $args['sort']            = 'comments.id';
		// $args['order']           = 'desc';
		// $args['comments.type']   = 'ticket';
		// $args['comments.status'] = ["NOT IN ", "('deleted')"];
		// $args['comments.parent'] = null;
		// $args['pagenation']      = false;
		// $args['limit']           = 5;
		// $args['join_user']       = true;
		// $args['get_tag']         = true;
		// // $args['comments.status'] = ["NOT IN", "('close')"];

		// \content_support\view::dataList($args);
	}

	public static function helpDashboard()
	{
		\dash\data::listCats(\dash\app\term::cat_list('help'));

		$randomArticles = \dash\app\posts::random_post(['type' => 'help', 'limit' => 10, 'status' => 'publish']);

		\dash\data::randomArticles($randomArticles);

		$randomFAQ = \dash\db\posts::get_posts_term(['type' => 'help', 'limit' => 10, 'tag' => 'faq', 'random' => true], 'tag');
		\dash\data::randomFAQ($randomFAQ);

	}
}
?>