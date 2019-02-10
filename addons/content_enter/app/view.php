<?php
namespace content_enter\app;


class view extends \content_enter\home\view
{

	public static function config()
	{
		parent::config();

		\dash\data::page_special(true);
		\dash\data::page_title(T_('Enter to :name', ['name' => \dash\data::site_title()]));
		\dash\data::page_desc(\dash\data::page_title());

	}
}
?>