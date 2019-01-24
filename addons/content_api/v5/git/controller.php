<?php
namespace content_api\v5\git;


class controller
{
	public static function routing()
	{
		if(\dash\url::isLocal() && \dash\url::subchild() === 'trans')
		{
			\dash\utility\twigTrans::extract('current', null);
			\dash\utility\twigTrans::extract('addons', null);
			\dash\code::boom();
		}
	}
}
?>