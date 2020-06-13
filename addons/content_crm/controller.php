<?php
namespace content_crm;

class controller
{

	public static function routing()
	{
		if(\dash\option::config('no_subdomain'))
		{
			\dash\redirect::remove_subdomain();
		}

		\dash\redirect::to_login();
		\dash\permission::access('contentCrm');
	}
}
?>