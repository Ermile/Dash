<?php
namespace content_cp\backup;

class model
{
	public static function post()
	{
		if(\dash\request::post('backup') === 'now')
		{
			if(self::backup_db())
			{
				self::backup_project();
			}
			else
			{
				\dash\notif::ok(T_("Can not create backup"));
			}
			\dash\redirect::pwd();
		}
		else
		{
			\dash\notif::ok(T_("Dont!"));
			return false;
		}
	}


	private static function backup_db($_db_name = null)
	{
		if(\dash\db::backup_dump(['download' => false, 'db_name' => $_db_name]))
		{
			return true;
		}
		return false;
	}


	private static function backup_project()
	{
		self::clean_old();

		$zip_addr = \content_cp\backup\controller::backup_addr();
		\dash\file::makeDir($zip_addr, null, true);


		$file_name = "Backup_". date("Y_m_d_H_i_"). \dash\url::root(). '.zip';

		$zip = \dash\utility\zip::folder($zip_addr. $file_name, root);
		if($zip)
		{
			\dash\notif::ok(T_("Backup Complete"));
		}
		else
		{
			\dash\notif::ok(T_("Can not create backup"));
		}
	}


	private static function clean_old()
	{
		$oldBackup = @glob(\content_cp\backup\controller::backup_addr().'*');
		if($oldBackup && is_array($oldBackup))
		{
			foreach ($oldBackup as $key => $value)
			{
				unlink($value);
			}
		}
	}
}
?>
