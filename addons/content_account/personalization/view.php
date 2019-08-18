<?php
namespace content_account\personalization;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('Personalization'));
		\dash\data::page_desc(T_('Your data, activity, and preferences that help make our services more useful to you'));
		\dash\data::page_tbox(false);

		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Back to Account'));

		\dash\data::isLtr(\dash\language::current('direction') === 'ltr' ? true : false);
	}
}
?>
