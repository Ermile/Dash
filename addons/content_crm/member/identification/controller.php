<?php
namespace content_crm\member\identification;


class controller
{
	public static function routing()
	{
		\dash\permission::access('cpUsersEdit');

	}
}
?>