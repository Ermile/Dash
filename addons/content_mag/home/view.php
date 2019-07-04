<?php
namespace content_mag\home;

class view
{

	public static function config()
	{
		if(\dash\data::isMag())
		{
			\dash\data::display_magAdmin('content_mag/home/article.html');
			self::magazine();
		}
		else
		{
			\dash\data::display_magAdmin('content_mag/home/dashboard.html');
			self::magDashboard();
		}
	}


	public static function magazine()
	{
		\dash\data::page_title(T_("Magazine"));

		$master = \dash\data::moduelRow();
		if(!isset($master['id']))
		{
			return;
		}
		$subchildPost = \dash\db\posts::get(['type' => 'mag', 'parent' => $master['id'], 'status' => 'publish']);
		if(is_array($subchildPost))
		{
			$subchildPost = array_map(['\dash\app\posts', 'ready'], $subchildPost);
			\dash\data::subchildPost($subchildPost);
		}

		$master = \dash\app\posts::ready($master);
		\dash\data::datarow($master);


		// set back link
		\dash\data::badge_text(T_('Return to magazine list'));
		\dash\data::badge_link(\dash\url::here());

		// set page title
		if(isset($master['title']))
		{
			\dash\data::page_title($master['title']);
		}
		// set page desc
		if(isset($master['excerpt']))
		{
			\dash\data::page_desc($master['excerpt']);
		}
		// set page desc
		if(isset($master['meta']['icon']))
		{
			\dash\data::page_pictogram($master['meta']['icon']);
		}



	}


	public static function magDashboard()
	{

		$get_posts_term =
		[
			'type'     => 'mag',
			'parent'   => null,
			'language' => \dash\language::current(),
		];

		if(\dash\permission::check('cpMagEditForOthers'))
		{
			$get_posts_term['status']   = ["NOT IN", "('deleted')"];
		}
		else
		{
			$get_posts_term['status']   = 'publish';
		}

		$search = \dash\request::get('q');

		if($search)
		{

			$get_search = $get_posts_term;
			unset($get_search['parent']);
			$dataTable = \dash\app\posts::list($search, $get_search);
			\dash\data::dataTable($dataTable);
		}

		$pageList = \dash\db\posts::get($get_posts_term);
		$pageList = array_map(['\dash\app\posts', 'ready'], $pageList);

		\dash\data::listCats($pageList);

		$randomArticles = \dash\app\posts::random_post(['type' => 'mag', 'limit' => 5, 'status' => 'publish', 'parent' => ["IS", "NOT NULL"]]);

		\dash\data::randomArticles($randomArticles);

		$randomFAQ = \dash\db\posts::get_posts_term(['type' => 'mag', 'limit' => 5, 'tag' => 'faq', 'random' => true], 'mag_tag');
		\dash\data::randomFAQ($randomFAQ);

	}


}
?>

