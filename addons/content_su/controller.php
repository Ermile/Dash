<?php
namespace content_su;

class controller
{
	public static function routing()
	{
		self::_permission();
	}


	public static function _permission()
	{
		// if user is not login then redirect
		if(\dash\url::module() === 'install' && \dash\request::get('time') == 'first_time')
		{
			// turn routing on and allow to check in module
			return false;
		}

		if(!\dash\user::login())
		{
			\dash\redirect::to(\dash\url::base(). '/enter');
			return ;
		}

		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		if(\dash\url::isLocal() && false)
		{
			// on tld dev open the su to upgrade for test
		}
		else
		{
			if(\dash\permission::access_su())
			{
				// the user have permission of su
			}
			else
			{
				// set 404 to the user never underestand this url is exist ;)
				\dash\header::status(404);
			}
		}
	}
}
?>