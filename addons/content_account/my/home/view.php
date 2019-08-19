<?php
namespace content_account\my\home;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Personal info'));
		\dash\data::page_desc(T_('Basic info, like your name and photo, that you use on our services'));
		\dash\data::page_tbox(false);

		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Back to Account'));

		self::load_me();

		\dash\data::isLtr(\dash\language::current('direction') === 'ltr' ? true : false);

		if(\dash\data::dataRow_permission())
		{
			$myPermName = \dash\data::dataRow_permission();
			$perm_list = \dash\permission::groups();
			if(isset($perm_list[$myPermName]['title']))
			{
				\dash\data::permName(T_($perm_list[$myPermName]['title']));
			}
			else
			{
				\dash\data::permName(T_(ucfirst($myPermName)));
			}
		}
	}


	public static function load_me()
	{

		$id = \dash\user::id();

		if(!$id)
		{
			\dash\header::status(404, T_("Invalid user id"));
		}

		$user_detail = \dash\db\users::get_by_id($id);

		if(!$user_detail)
		{
			\dash\header::status(404, T_("User id not found"));
		}

		\dash\data::dataRow(\dash\app\user::ready($user_detail, true));
	}
}
?>
