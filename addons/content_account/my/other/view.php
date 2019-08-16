<?php
namespace content_account\my\other;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Edit your profile'));
		\dash\data::page_desc(T_('You can edit your profile.'));

		\dash\data::badge_link(\dash\url::kingdom(). '/a');
		\dash\data::badge_text(T_('Back to dashbaord'));

		\content_account\my\view::load_me();
	}
}
?>
