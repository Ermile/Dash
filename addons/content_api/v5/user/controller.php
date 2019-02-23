<?php
namespace content_api\v5\user;


class controller
{
	public static function routing()
	{
		\content_api\v5::check_authorization2_v5();

		switch (\dash\url::subchild())
		{
			case 'add':
				if(\dash\request::is('get'))
				{
					\dash\header::status(400);
				}
				elseif(\dash\request::is('post'))
				{
					\content_api\v5\user\user_add::add();
				}
				break;

			default:
				\dash\header::status(404);
				break;
		}
	}
}
?>