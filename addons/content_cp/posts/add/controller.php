<?php
namespace content_cp\posts\add;

class controller
{
	public static function routing()
	{
		\dash\permission::access('cpPostsAdd');
	}
}
?>