<?php
namespace content_su\tg;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Telegram"));
		\dash\data::page_desc(T_('Check Telegram bot api status and play with it.'));
		\dash\data::page_pictogram('paper-plane');
	}
}
?>