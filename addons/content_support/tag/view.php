<?php
namespace content_support\tag;

class view
{

	public static function config()
	{

		\dash\data::page_title(T_("Category"));
		\dash\data::page_desc(T_("Easily manage your tickets and monitor or track them to get best answer until fix your problem"));
		\dash\data::page_pictogram('life-ring');

		\dash\data::badge_text(T_('Tickets'));
		\dash\data::badge_link(\dash\url::here(). '/ticket'. \dash\data::accessGet());

		$postTag = \dash\db\posts::get_posts_term(['type' => 'help', 'limit' => 100, 'tag' => urldecode(\dash\url::child())], 'help_tag');

		\dash\data::postTag($postTag);

		\content_support\home\view::helpDashboard();

	}
}
?>