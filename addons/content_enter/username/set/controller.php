<?php
namespace content_enter\username\set;

class controller
{
	public static function routing()
	{
		if(\dash\user::login('username'))
		{
			\dash\redirect::to(\dash\url::kingdom(). '/enter/username/change');
			return;
		}
	}
}
?>