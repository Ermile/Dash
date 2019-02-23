<?php
namespace content_api\v6\user;


class controller
{
	public static function routing()
	{
		\content_api\v6::check_authorization2_v6();

		switch (\dash\url::subchild())
		{
			case 'add':
				if(\dash\request::is('get'))
				{
					\dash\header::status(400);
				}
				elseif(\dash\request::is('post'))
				{
					\content_api\v6\user\user_add::add();
				}
				break;

			default:
				\dash\header::status(404);
				break;
		}
	}
}
?>