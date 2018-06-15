<?php
namespace content_su\linfo;

class controller
{
	public static function routing()
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && !class_exists("COM"))
		{
			return;
		}

		require addons.'lib/linfo2/index.php';
		\dash\code::exit();
	}
}
?>