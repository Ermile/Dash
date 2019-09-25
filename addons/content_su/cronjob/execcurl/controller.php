<?php
namespace content_su\cronjob\execcurl;


class controller
{
	public static function routing()
	{
		$addr = core. 'lib/engine/cronjob/cronjob.php';
		if(is_file($addr))
		{
			include_once($addr);
		}
	}
}
?>