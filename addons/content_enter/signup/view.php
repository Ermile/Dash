<?php
namespace content_enter\signup;

class view
{

	public static function config()
	{
		\dash\data::page_special(true);
		\dash\data::page_title(T_('Signup in :name' , ['name' => \dash\data::site_title()]));
		\dash\data::page_desc(\dash\data::page_title());

		// set el value to use in display
		\dash\data::el_username(\dash\option::config('enter', 'singup_username'));
	}
}
?>