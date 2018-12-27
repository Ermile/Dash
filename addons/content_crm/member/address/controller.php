<?php
namespace content_crm\member\address;


class controller
{
	public static function routing()
	{
		\dash\permission::access('aMemberView');
	}
}
?>