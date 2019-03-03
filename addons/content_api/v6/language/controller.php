<?php
namespace content_api\v6\language;


class controller
{
	public static function routing()
	{
		if(\dash\url::subchild())
		{
			\content_api\v6::no(404);
		}

		$result = \dash\language::all();

		\content_api\v6::bye($result);
	}
}
?>