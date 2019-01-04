<?php
namespace content_hook\app\android;


class controller
{
	public static function routing()
	{
		$detail = self::detail();

		\dash\code::jsonBoom($detail);
	}

	private static function detail()
	{
		$detail            = [];
		$detail['version'] = '1.1.1';
		return $detail;
	}
}
?>