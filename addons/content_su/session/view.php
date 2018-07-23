<?php
namespace content_su\session;


class view
{
	public static function config()
	{
		\dash\log::db('sessionView');
		\dash\data::session($_SESSION);
	}
}
?>