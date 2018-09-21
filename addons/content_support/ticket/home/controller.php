<?php
namespace content_support\ticket\home;

class controller
{


	public static function routing()
	{
		if(!\dash\user::login())
		{
			// \dash\redirect::to(\dash\url::base(). '/enter');
			// return;
		}
	}
}
?>