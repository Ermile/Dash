<?php
namespace content_cp\dayevent;


class controller
{
	public static function routing()
	{
		\dash\permission::access('cpDayEvent');
	}
}
?>
