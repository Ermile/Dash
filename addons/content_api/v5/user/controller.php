<?php
namespace content_api\v5\user;


class controller
{
	public static function routing()
	{
		switch (\dash\url::subchild())
		{
			case 'add':
				\content_api\v5\user\add\controller::routing();
				break;

			default:
				\dash\header::status(404);
				break;
		}
	}


}
?>