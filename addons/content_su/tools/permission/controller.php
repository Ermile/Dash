<?php
namespace content_su\tools\permission;

class controller
{
	public static function routing()
	{
		\dash\utility\permissionlist::extract();
		\dash\code::boom();
	}

}
?>