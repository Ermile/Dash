<?php
namespace content_cms\dayevent;


class controller
{
	public static function routing()
	{
		\dash\permission::access('cpDayEvent');
	}
}
?>
