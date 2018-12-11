<?php
namespace content_crm\sms\template;


class view
{
	public static function config()
	{
		\dash\permission::access('cpSmsTemplateView');

		\dash\data::page_pictogram('envelope-o');
		\dash\data::page_title(T_("Template SMS"));
		\dash\data::page_desc(T_("check and update some options on template sms"));
		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Back to dashboard'));
		\dash\data::bodyclass('unselectable');
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);

		$list = \dash\app\smstemplate::list();
		\dash\data::templateList($list);

		if($name = \dash\request::get('name'))
		{
			if(is_array($list) && array_key_exists($name, $list))
			{
				\dash\data::dataRow(['name' => $name, 'text' => $list[$name]]);
				\dash\data::editMode(true);
			}
		}
	}
}
?>
