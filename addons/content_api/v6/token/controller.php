<?php
namespace content_api\v6\token;


class controller
{
	public static function routing()
	{
		if(\dash\url::subchild())
		{
			\content_api\v6::no(404);
		}

		\content_api\v6::check_appkey();

		$result = \dash\app\user_auth::make();

		\content_api\v6::bye($result);
	}
}
?>