<?php
namespace content_crm\member\add;


class controller
{

	public static function routing()
	{
		\dash\permission::access('cpUsersAdd');
	}
}
?>