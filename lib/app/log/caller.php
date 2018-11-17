<?php
namespace dash\app\log;

class caller
{

	public static function title($_string = null)
	{
		$arg          = [];
		$arg['title'] = $_string;
		return $arg;
	}

	public static function before_add()
	{
		$args          = [];
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