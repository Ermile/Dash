<?php
namespace content_su\permission;

class controller
{
	public static function routing()
	{
		\dash\utility\permissionlist::extract();
		\dash\code::boom();
	}

}
?>