<?php
namespace content_cp\sms\template;


class view
{
	public static function config()
	{
		\dash\permission::access('cpTemplateSMS');

		\dash\data::page_title(T_("Template SMS"));
		\dash\data::page_desc(T_("check and update some options on template sms"));
		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Back to dashboard'));
		\dash\data::bodyclass('unselectable');
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);
		\dash\data::templateList(\lib\app\donate::way_list('sms'));
	}
}
?>
