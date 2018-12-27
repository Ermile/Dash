<?php
namespace content_crm\member\security;


class controller
{
	public static function routing()
	{
		\dash\permission::access('cpUsersEdit');
	}
}
?>