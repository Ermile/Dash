<?php
namespace content_api\v5\git;


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