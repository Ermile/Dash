<?php
namespace content_su\tg\sendmessage;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Send message"));
		\dash\data::page_desc(T_('Send simple text message to selected user'));
		\dash\data::page_pictogram('envelope');
	}
}
?>