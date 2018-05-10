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
			\dash\permission::access('cotentCp');
		}
	}
}
?>