<?php
namespace content_enter\twostep;


class view
{
	public static function config()
	{
		if(!\dash\user::login())
		{
			\dash\header::status(403, T_("Please login to continue"));
		}

		\dash\data::page_title(T_('Two step verification'));
		\dash\data::page_special(true);
		\dash\data::page_desc(T_('You can trun it on or off'));



	}
}
?>