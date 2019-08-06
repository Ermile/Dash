<?php
namespace content_support\faq;

class view
{

	public static function config()
	{

		\dash\data::page_title(T_("FAQ"));
		\dash\data::page_desc(T_("Easily manage your tickets and monitor or track them to get best answer until fix your problem"));
		\dash\data::page_pictogram('life-ring');

		\dash\data::badge_text(T_('Tickets'));
		\dash\data::badge_link(\dash\url::here(). '/ticket'. \dash\data::accessGet());

		if(\dash\url::subdomain() && !\dash\option::config('no_subdomain'))
		{
			$postTag = \dash\db\posts::get_posts_term(['type' => 'help', 'limit' => 100, 'tag' => 'faq', 'subdomain' => \dash\url::subdomain()], 'help_tag');
		}
		else
		{
			$postTag = \dash\db\posts::get_posts_term(['type' => 'help', 'limit' => 100, 'tag' => 'faq'], 'help_tag');
		}

		\dash\data::postTag($postTag);

		\content_support\home\view::helpDashboard();

	}
}
?>