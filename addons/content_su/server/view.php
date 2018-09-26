<?php
namespace content_su\server;


class view
{
	public static function config()
	{
		\dash\log::set('serverView');
		\dash\data::server($_SERVER);
	}
}
?>