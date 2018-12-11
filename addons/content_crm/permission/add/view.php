<?php
namespace content_crm\permission\add;


class view
{
	public static function config()
	{
		\dash\permission::access('cpPermissionAdd');

		\dash\data::page_title(T_("Add new permissions"));
		\dash\data::page_desc(T_("Set and config permission group to categorize user access."));

		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back to list of permissions'));

		\dash\data::page_pictogram('lock');

		\dash\data::perm_list(\dash\permission::categorize_list());
		\dash\data::perm_group(\dash\permission::groups());

		if(\dash\request::get('id'))
		{
			\dash\permission::access('cpPermissionEdit');

			$id = \dash\request::get('id');
			$load_permission = \dash\permission::load_permission($id);

			if(!$load_permission)
			{
				\dash\header::status(404, T_("Invalid permission id"));
			}

			\dash\data::page_title(T_("Edit permission :name" , ['name' => $load_permission['title']]));

			\dash\data::perm_load($load_permission);
		}
	}
}
?>