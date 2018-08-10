<?php
namespace content_su\tg\sendphoto;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Send photo"));
		\dash\data::page_desc(T_('Quickly send photo to selected user'));
		\dash\data::page_pictogram('picture-o');
		\dash\data::badge_text(T_('Back to Telegram dashboard'));
		\dash\data::badge_link(\dash\url::this());


		\dash\data::tg_send(\dash\session::get('tg_send'));
		\dash\data::tg_response(\dash\session::get('tg_response'));

		\dash\session::set('tg_send', null);
		\dash\session::set('tg_response', null);
	}
}
?>