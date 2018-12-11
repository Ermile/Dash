<?php
namespace content_crm\sms\template;


class model
{

	public static function post()
	{
		$edit = false;

		\dash\permission::access('cpSmsTemplateEdit');

		if(\dash\request::post('type') === 'delete' && \dash\request::post('name'))
		{
			if(\dash\app\smstemplate::remove(\dash\request::post('name')))
			{
				\dash\notif::warn(T_("The template successfully removed"));
			}
			else
			{
				return;
			}
		}
		else
		{


			$post         = [];
			$post['name'] = \dash\request::post('name');
			$post['text'] = \dash\request::post('text');

			$edit = \dash\request::get('name');

			\dash\app\smstemplate::set($post, $edit);

			if(\dash\engine\process::status())
			{
				if($edit)
				{
					\dash\notif::ok(T_("Sms template successfully updated"));
				}
				else
				{
					\dash\notif::ok(T_("Sms template successfully added"));
				}
			}
		}

		if(\dash\engine\process::status())
		{
			if($edit)
			{
				\dash\redirect::to(\dash\url::this(). '/template?name='. \dash\request::post('name'));
			}
			else
			{
				\dash\redirect::pwd();
			}
		}
	}
}
?>
