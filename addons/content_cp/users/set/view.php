<?php
namespace content_cp\users\set;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Add new user'));
		\dash\data::page_desc(T_('You can add new user and after add with minimal data, we allow you to add extra detail of user.'));


		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back to list of users'));

		$perm_list = \dash\permission::groups();
		\dash\data::permGroup(array_keys($perm_list));
	}
}
?>