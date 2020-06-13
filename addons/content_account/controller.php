<?php
namespace content_account;

class controller
{
	/**
	 * rout
	 */
	public static function routing()
	{
		if(\dash\option::config('no_subdomain'))
		{
			\dash\redirect::remove_subdomain();
		}

		\dash\redirect::to_login();
		\content_account\load::me();
	}
}
?>