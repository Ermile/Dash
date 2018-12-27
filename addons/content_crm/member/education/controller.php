<?php
namespace content_crm\member\education;


class controller
{
	public static function routing()
	{
		\dash\permission::access('cpUsersEdit');
	}
}
?>