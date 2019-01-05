<?php
namespace content_hook\android\detail;


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

		if(is_callable(["\\lib\\app\\android", "detail"]))
		{
			$my_detail = \lib\app\android::detail();
			if(is_array($my_detail))
			{
				$detail = array_merge($detail, $my_detail);
			}
		}

		return $detail;
	}
}
?>