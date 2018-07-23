<?php
namespace content_su\server;


class view
{
	public static function config()
	{
		\dash\log::db('serverView');
		\dash\data::server($_SERVER);
	}
}
?>