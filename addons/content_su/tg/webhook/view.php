<?php
namespace content_su\tg\webhook;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Telegram webhook"));
		\dash\data::page_desc(T_('Show telegram webhook information'));
		\dash\data::page_pictogram('link');
		\dash\data::badge_text(T_('Back to Telegram dashboard'));
		\dash\data::badge_link(\dash\url::this());


		// \dash\data::hook(\dash\social\telegram\tg::getWebhookInfo());
	}
}
?>