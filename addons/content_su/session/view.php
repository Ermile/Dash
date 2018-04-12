<?php
namespace content_su\session;


class view
{
	public static function config()
	{
		\dash\data::session($_SESSION);
	}
}
?>