<?php
namespace content_cms\mycontact;

class view
{
	public static function config()
	{
		\dash\data::page_pictogram('comment');
		\dash\data::page_title(T_("Comment Dashboard"));
		\dash\data::page_desc(T_("Comment Dashboard"));
	}
}
?>