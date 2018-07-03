<?php
namespace content_support;

class controller
{
	/**
	 * rout
	 */
	public static function routing()
	{
		if(!\dash\user::login())
		{
			\dash\redirect::to(\dash\url::base(). '/enter');
			return;
		}

	}
}
?>