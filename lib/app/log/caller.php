<?php
namespace dash\app\log;

class caller
{

	public static function before_add()
	{
		$args   = [];
		return $args;
	}


	public static function is_notif()
	{
		return false; // or true if need
	}


	public static function list($_args = [])
	{
		$args            = [];
		$args['title']   = null;
		$args['content'] = null;
		return $args;
	}


	public static function displayname($_args)
	{
		if(isset($_args['displayname']))
		{
			return $_args['displayname'];
		}
		return null;
	}


	public static function load()
	{

	}

	public static function send()
	{

	}
}
?>