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

		$parent = null;
		if(isset(\content_api\v6::$v6['appkey_detail']['id']))
		{
			$parent = \content_api\v6::$v6['appkey_detail']['id'];
		}

		$result = \dash\app\user_auth::make(['parent' => $parent]);

		\content_api\v6::bye($result);
	}
}
?>