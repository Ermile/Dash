<?php
namespace dash\app\log\caller;

class testLog extends \dash\app\log\caller
{
	public static function before_add()
	{
		$args          = [];
		$args['notif'] = 1;
		return $args;
	}

	public static function list()
	{

	}


	public static function load()
	{

	}


	public static function send()
	{

	}
}
?>
