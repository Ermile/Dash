<?php
namespace content_cp\backup;

class model
{
	public static function post()
	{
		\dash\notif::warn(T_("Not ready"));

		// if(\dash\request::post('backup') === 'now')
		// {
		// 	if(self::backup_db())
		// 	{
		// 		self::backup_project();
		// 	}
		// }
		// else
		// {
		// 	\dash\notif::ok(T_("Dont!"));
		// 	return false;
		// }
	}

	private static function backup_db($_db_name = null)
	{
		if(\dash\db::backup_dump(['download' => false, 'db_name' => $_db_name]))
		{
			\dash\notif::ok(T_("Database Backup complete"));
			return true;
		}
		return false;
	}


	private static function backup_project()
	{
		$x = \dash\utility\zip::create('/home/reza/1.zip', root);
		// var_dump($x);exit();
	}

}
?>
