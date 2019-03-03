<?php
namespace content_api\v6\android;


class controller
{
	public static function routing()
	{
		if(\dash\url::directory() === 'v6/android/user/add' && \dash\request::is('post'))
		{
			\content_api\v6\android\user\add::add();
		}
		elseif(\dash\url::subchild())
		{
			\content_api\v6::no(404);
		}

		$detail = self::detail();

		\content_api\v6::bye($detail);
	}

	private static function detail()
	{
		$detail            = [];
		$detail['version'] = '1.1.1';

		$detail['lang_list'] = \dash\language::all();

		if(is_callable(["\\lib\\app\\android", "detail_v6"]))
		{
			$my_detail = \lib\app\android::detail_v6();
			if(is_array($my_detail))
			{
				$detail = array_merge($detail, $my_detail);
			}
		}

		return $detail;
	}
}
?>