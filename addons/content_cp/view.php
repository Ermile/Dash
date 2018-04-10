<?php
namespace content_cp;

class view
{
	public static function config()
	{
		\dash\data::bodyclass('siftal');
		\dash\data::include_editor(true);
		\dash\data::badge_shortkey(120);
	}
}
?>