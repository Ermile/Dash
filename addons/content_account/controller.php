<?php
namespace content_account;

class controller
{
	/**
	 * rout
	 */
	public static function routing()
	{
		if(!\dash\user::login())
		{
			\dash\redirect::to(\dash\url::kingdom(). '/enter', 'direct');
			return;
		}

	}
}
?>