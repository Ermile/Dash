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
}
?>