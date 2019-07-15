<?php
namespace content_support\ticket\tags;

class controller
{
	public static function routing()
	{
		\dash\permission::access('cpTagSupportEdit');
	}
}
?>
