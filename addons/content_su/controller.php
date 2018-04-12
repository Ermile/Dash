<?php
namespace content_su;

class controller
{

	/**
	 * check if not installed database
	 * install databse
	 */
	public static function routing()
	{
		self::install_dash();
		self::_permission();
	}


	private static function install_dash()
	{
		// run if get is set and no database exist
		if(\dash\url::module() === 'install' && \dash\request::get('time') == 'first_time')
		{
			if(!\dash\db::count_table())
			{
				require_once(lib."engine/install.php");
				// this code exit the code
				\dash\code::end();
			}
			else
			{
				\dash\header::status(404, T_("System was installed!"));
			}
		}
	}



	public static function _permission()
	{
		// if user is not login then redirect
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