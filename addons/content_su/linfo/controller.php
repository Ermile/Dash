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
		\dash\log::set('linfoView');
		require addons.'lib/linfo/index.php';
		\dash\code::boom();
	}
}
?>