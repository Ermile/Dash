<?php
namespace content_cp\users\set;


class view
{

	public static function config()
	{
		\dash\permission::access('cpUsersEdit');

		\dash\data::page_title(T_('Add new user'));
		\dash\data::page_desc(T_('You can add new user and after add with minimal data, we allow you to add extra detail of user.'));


		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back to list of users'));

		$perm_list = \dash\permission::groups();
		\dash\data::permGroup(array_keys($perm_list));

		if(\dash\request::get('id'))
		{
			$id = \dash\coding::decode(\dash\request::get('id'));
			if(!$id)
			{
				\dash\header::status(404, T_("Invalid user id"));
			}

			$user_detail = \dash\db\users::get_by_id($id);
			if(!$user_detail)
			{
				\dash\header::status(404, T_("User id not found"));
			}

			\dash\data::dataRaw(\dash\app\user::ready($user_detail, true));

		}
	}
}
?>