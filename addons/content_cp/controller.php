<?php
namespace content_cp;

class controller
{

	public static function routing()
	{

		if(!\dash\user::login())
		{
			\dash\redirect::to(\dash\url::base(). '/enter?referer='. \dash\url::pwd());
			return;
		}

		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		if(\dash\url::isLocal())
		{
			// on tld dev open the cp to upgrade for test
		}
		else
		{
			if(\dash\permission::check('cp'))
			{
				// the user have permission of cp
			}
			else
			{
				\dash\header::status(403, T_("Can not access to cp"));
			}
		}
	}
}
?>