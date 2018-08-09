<?php
namespace content_su\tg\sendphoto;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Send photo"));
		\dash\data::page_desc(T_('Quickly send photo to selected user'));
		\dash\data::page_pictogram('picture-o');
	}
}
?>