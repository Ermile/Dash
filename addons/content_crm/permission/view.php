<?php
namespace content_crm\permission;


class view
{
	public static function config()
	{
		\dash\permission::access('cpPermissionView');

		\dash\data::page_title(T_("Permissions"));
		\dash\data::page_desc(T_("Set and config permission of users and allow them to do something."));

		\dash\data::page_pictogram('unlock-alt');

		\dash\data::badge_link(\dash\url::this().'/add');
		\dash\data::badge_text(T_('Add new permission'));

		\dash\data::perm_list(\dash\permission::categorize_list());
		\dash\data::perm_group(\dash\permission::groups());
		\dash\data::perm_groupPos(\dash\permission::groups(true));
		\dash\data::perm_usercount(\dash\permission::usercount());


	}
}
?>