<?php
namespace content_cp\sms\group;

class view
{
	public static function config()
	{
		\dash\permission::access('cpSmsSend');

		\dash\data::page_title(T_("Send Sms to group of users"));
		\dash\data::page_desc(T_("Send every sms to every group users by mobile"));


		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Dashboard'));

		\dash\data::bodyclass('unselectable');
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);


		$mobile = \dash\request::get('mobile');
		$mobile = \dash\utility\filter::mobile($mobile);
		if($mobile)
		{
			\dash\data::userInfo(\dash\db\users::get_by_mobile($mobile));
		}
	}
}
?>