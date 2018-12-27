<?php
namespace content_crm\member\general;


class controller
{
	public static function routing()
	{
		\dash\permission::access('cpUsersEdit');
	}
}
?>