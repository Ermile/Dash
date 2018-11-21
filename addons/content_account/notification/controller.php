<?php
namespace content_account\notification;

class controller
{

	public static function routing()
	{
		if(\dash\url::child() === 'archive' && !\dash\url::subchild())
		{
			\dash\open::get();
		}
	}
}
?>