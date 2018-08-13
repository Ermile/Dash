<?php
namespace content_enter\username\change;

class controller
{
	public static function routing()
	{

		if(!\dash\user::login('username'))
		{
			\dash\redirect::to(\dash\url::kingdom(). '/enter/username/set');
			return;
		}
	}
}
?>