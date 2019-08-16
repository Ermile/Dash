<?php
namespace content_account\my\profile;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Personal info'));
		\dash\data::page_desc(T_('Basic info, like your name and photo, that you use on our services'));

		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Back to Account'));

		\content_account\my\view::load_me();

		\dash\data::isLtr(\dash\language::current('direction') === 'ltr' ? true : false);
	}
}
?>
