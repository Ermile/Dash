<?php
namespace content_su\server;


class view
{
	public static function config()
	{
		\dash\data::server($_SERVER);
	}
}
?>