<?php
namespace content_cp\sms\template;


class model
{

	public static function post()
	{
		\dash\permission::access('cpTemplateSMS');

		if(\dash\request::post('type') === 'delete' && \dash\request::post('key'))
		{
			if(\lib\app\donate::remove_way(\dash\request::post('key'), 'sms'))
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
			$way = \dash\request::post('way');

			\lib\app\donate::set_way($way, false, 'sms');
			if(\dash\engine\process::status())
			{
				\dash\notif::ok(T_("Sms template successfully added"));
			}
		}

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
