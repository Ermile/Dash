<?php
namespace content_hook\gitdetail;


class view
{
	public static function config()
	{
		$file = root. '/gitdetail.me.json';
		if(is_file($file))
		{
			$get = \dash\file::read($file);
			echo $get;
		}
		\dash\code::boom();
	}
}
?>