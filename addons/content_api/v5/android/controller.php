<?php
namespace content_api\v5\android;


class controller
{
	public static function routing()
	{
		$detail = self::detail();

		\content_api\v5::old_end5($detail);
	}

	private static function detail()
	{
		$detail            = [];
		$detail['version'] = '1.1.1';

		$detail['lang_list'] = \dash\language::all();

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