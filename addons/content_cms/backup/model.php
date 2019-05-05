<?php
namespace content_cms\backup;

class model
{
	// save backup in this folder
	private static function backup_addr()
	{
		return root. 'public_html/files/backup/';
	}


	public static function post()
	{
		\dash\log::set('fullBackup');
		if(\dash\request::post('backup') === 'now')
		{
			self::clean_old();

			\dash\file::makeDir(self::backup_addr(), null, true);

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
		elseif(\dash\request::post('type') === 'remove')
		{
			$file_name = \dash\request::post('file');
			if(\dash\file::delete(self::backup_addr(). $file_name))
			{
				\dash\notif::ok(T_("File successfully deleted"));
				\dash\redirect::pwd();
				return;
			}
		}
		else
		{
			\dash\notif::ok(T_("Dont!"));
			return false;
		}
	}


	private static function backup_db($_db_name = null)
	{
		if(\dash\db::backup_dump(['download' => false, 'db_name' => $_db_name, 'dir' => self::backup_addr()]))
		{
			if(defined('db_log_name'))
			{
				\dash\db::backup_dump(['download' => false, 'db_name' => db_log_name, 'dir' => self::backup_addr()]);
			}

			return true;
		}
		return false;
	}


	private static function backup_project()
	{

		$zip_addr = self::backup_addr();


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
		$oldBackup = @glob(self::backup_addr().'*');
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
