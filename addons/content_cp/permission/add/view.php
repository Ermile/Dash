<?php
namespace content_cp\permission\add;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Add new permissions"));
		\dash\data::page_desc(T_("Set and config permission group to categorize user access."));

		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back to list of permissions'));


		\dash\data::perm_list(\dash\permission::categorize_list());
		\dash\data::perm_group(\dash\permission::groups());

	}
}
?>