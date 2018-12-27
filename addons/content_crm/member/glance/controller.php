<?php
namespace content_crm\member\glance;


class controller
{
	public static function routing()
	{
		\dash\permission::access('cpUsersView');
	}
}
?>