<?php
namespace content_crm\users\set;


class view
{

	public static function config()
	{
		\dash\permission::access('cpUsersView');

		\dash\data::page_title(T_('Add new user'));
		\dash\data::page_desc(T_('You can add new user and after add with minimal data, we allow you to add extra detail of user.'));
		\dash\data::page_pictogram('user-plus');


		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back to list of users'));

		$perm_list = \dash\permission::groups();

		\dash\data::permGroup($perm_list);

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

			if(isset($user_detail['permission']))
			{
				if($user_detail['permission'] === 'supervisor' && !\dash\url::isLocal() && !\dash\permission::supervisor())
				{
					\dash\header::status(404, T_("User not found"));
				}
			}

			\dash\data::dataRow(\dash\app\user::ready($user_detail, true));

			// set page title on edit
			\dash\data::page_title(T_('Edit profile of :val', ['val' => $user_detail['displayname']]));
			\dash\data::page_desc(T_('Edit user detail and change all fields of this user, be careful!'));
			\dash\data::page_pictogram('user-secret');
		}
	}
}
?>