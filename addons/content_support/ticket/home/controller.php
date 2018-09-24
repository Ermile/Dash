<?php
namespace content_support\ticket\home;

class controller
{


	public static function routing()
	{
		if(!\dash\request::get('access') && \dash\permission::check('supportTicketManage'))
		{
			\dash\redirect::to(\dash\url::this(). '?access=manage');
			return;
		}
	}
}
?>