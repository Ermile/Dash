<?php
namespace content_crm\member\contact;


class controller
{
	public static function routing()
	{
		\dash\permission::access('cpUsersEdit');
	}
}
?>