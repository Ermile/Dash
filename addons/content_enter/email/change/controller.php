<?php
namespace content_enter\email\change;


class controller
{
	public static function routing()
	{
		if(!\dash\user::login('email'))
		{
			\dash\redirect::to(\dash\url::kingdom(). '/enter/email/set');
			return;
		}
	}
}
?>