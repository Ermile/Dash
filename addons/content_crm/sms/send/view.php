<?php
namespace content_crm\sms\send;

class view
{
	public static function config()
	{
		\dash\permission::access('cpSmsSend');

		\dash\data::page_title(T_("Send Sms to user"));
		\dash\data::page_desc(T_("Send every sms to every user by mobile"));

		\dash\data::page_pictogram('envelope-o');

		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Dashboard'));

		\dash\data::bodyclass('unselectable');
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);


		$mobile = \dash\request::get('mobile');
		$mobile = \dash\utility\filter::mobile($mobile);
		if($mobile)
		{
			\dash\data::userInfo(\dash\app\user::ready(\dash\db\users::get_by_mobile($mobile)));
		}

		$templateList = \dash\app\smstemplate::list();
		\dash\data::templateList($templateList);

		$template_get = \dash\request::get('template');


		if($template_get && is_string($template_get) && array_key_exists($template_get, $templateList))
		{
			\dash\data::templateLoad($templateList[$template_get]);
		}
	}
}
?>