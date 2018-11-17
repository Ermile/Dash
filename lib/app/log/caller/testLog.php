<?php
namespace dash\app\log\caller;

class testLog extends \dash\app\log\caller
{

	public static function before_add()
	{

	}

	public static function is_nofit()
	{
		return true;
	}

	public static function list($_args = [])
	{
		$args            = [];
		$args['title']   = T_("Hi");
		$args['content'] = T_("Ermile");
		return $args;
	}


	public static function load()
	{

	}


	public static function send()
	{

	}
}
?>
