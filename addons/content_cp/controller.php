<?php
namespace content_cp;

class controller
{

	public static function routing()
	{

		if(!\dash\user::login())
		{
			\dash\redirect::to(\dash\url::kingdom(). '/enter?referer='. \dash\url::pwd());
			return;
		}

		\dash\permission::access('contentCp');

	}
}
?>