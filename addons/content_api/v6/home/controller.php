<?php
namespace content_api\v6\home;


class controller
{
	public static function routing()
	{
		if(\dash\url::child() && !\dash\url::subchild() && in_array(\dash\url::child(), ['mission', 'vision', 'about', 'contact']))
		{
			\content_api\v6\static_page::run(\dash\url::child());
		}
		else
		{
			\content_api\home\controller::routing();
		}
	}
}
?>