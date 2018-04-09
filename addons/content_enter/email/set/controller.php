<?php
namespace content_enter\email\set;

class controller
{
	public static function routing()
	{
		if(\dash\user::login('email'))
		{
			\dash\redirect::to(\dash\url::base(). '/enter/email/change');
			return;
		}
	}
}
?>