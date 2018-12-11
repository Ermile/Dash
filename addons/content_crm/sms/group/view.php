<?php
namespace content_crm\sms\group;

class view
{
	public static function config()
	{
		\dash\permission::access('cpSmsSendGroup');

		\dash\data::page_title(T_("Send Sms to group of users"));
		\dash\data::page_desc(T_("Send every sms to every group users by mobile"));

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
			\dash\data::userInfo(\dash\db\users::get_by_mobile($mobile));
		}

		$templateList = \dash\app\smstemplate::list();
		\dash\data::templateList($templateList);
		\dash\data::groupList(\dash\app\smsgroup::list());

		$template_get = \dash\request::get('template');


		if($template_get && is_string($template_get) && array_key_exists($template_get, $templateList))
		{
			\dash\data::templateLoad($templateList[$template_get]);
		}

		if(\dash\session::get('usersmobile_sms'))
		{
			\dash\data::usermobiles(\dash\session::get('usersmobile_sms'));
			\dash\session::set('usersmobile_sms', null);
		}
	}
}
?>