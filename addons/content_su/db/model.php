<?php
namespace content_su\db;


class model
{
	public static function post()
	{
		\dash\db::$link_open    = [];
		\dash\db::$link_default = null;
		if(\dash\request::post('username'))
		{
			\dash\db::$db_user = \dash\request::post("username");
			\dash\db::$db_pass = \dash\request::post("password");
		}
		elseif(defined('admin_db_user') && defined('admin_db_pass'))
		{
			\dash\db::$db_user = constant("admin_db_user");
			\dash\db::$db_pass = constant("admin_db_pass");
		}
		elseif(defined('db_user') && defined('db_pass'))
		{
			\dash\db::$db_user = constant("db_user");
			\dash\db::$db_pass = constant("db_pass");
		}
		else
		{
			\dash\header::status(403, T_("Permission denide for run upgrade database"));
		}

		\dash\db::$debug_error = false;

		$result = null;
		$exist  = true;

		if(\dash\request::post('type') == 'upgrade')
		{
			// do upgrade
			$result = \dash\db::install(true, true);
		}
		elseif(\dash\request::post('type') == 'backup')
		{
			// do backup
			$result = \dash\db::backup(true);
		}
		elseif(\dash\request::post('type') == 'backup_dump')
		{
			// do backup
			$result = \dash\db::backup_dump();
		}

		\dash\log::set('su_upgradeDataBase');

		\content_api\v5\git\model::save_detail(false);


		\dash\code::pretty($result, true);
		\dash\code::boom();


	}
}
?>