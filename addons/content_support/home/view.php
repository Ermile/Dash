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


		if(\dash\data::isHelpCenter())
		{
			\dash\data::display_supportAdmin('content_support/home/template.html');
			self::help_center();
		}
		else
		{
			\dash\data::display_supportAdmin('content_support/home/help.html');
			self::helpDashboard();
		}
	}

	public static function help_center()
	{
		$master = \dash\data::moduelRow();
		if(!isset($master['id']))
		{
			return;
		}
		$subchildPost = \dash\db\posts::get(['type' => 'help', 'parent' => $master['id'], 'status' => 'publish']);
		if(is_array($subchildPost))
		{
			$subchildPost = array_map(['\dash\app\posts', 'ready'], $subchildPost);
			\dash\data::subchildPost($subchildPost);
		}

		\dash\data::datarow($master);
		$master = \dash\app\posts::ready($master);
	}



	public static function helpDashboard()
	{
		$pageList = \dash\db\posts::get(['type' => 'help', 'parent' => null, 'language' => \dash\language::current(), 'status' => ["NOT IN", "('deleted')"]]);
		$pageList = array_map(['\dash\app\posts', 'ready'], $pageList);

		\dash\data::listCats($pageList);

		$randomArticles = \dash\app\posts::random_post(['type' => 'help', 'limit' => 10, 'status' => 'publish']);

		\dash\data::randomArticles($randomArticles);

		$randomFAQ = \dash\db\posts::get_posts_term(['type' => 'help', 'limit' => 10, 'tag' => 'faq', 'random' => true], 'tag');
		\dash\data::randomFAQ($randomFAQ);

	}
}
?>