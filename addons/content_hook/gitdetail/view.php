<?php
namespace content_hook\gitdetail;


class view
{
	public static function config()
	{
		$file = root. '/gitdetail.me.json';
		$get  = null;
		if(is_file($file))
		{
			$get = \dash\file::read($file);
		}
		\dash\code::jsonBoom($get);

	}
}
?>