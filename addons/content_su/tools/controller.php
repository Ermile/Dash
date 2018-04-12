<?php
namespace content_su\tools;

class controller
{
	public static function ready()
	{

		$exist    = false;

		$url_child = \dash\url::dir(1);

		switch ($url_child)
		{
			case 'db':

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

				\dash\code::print($result, true);
				\dash\code::exit();
				break;

			case null:
				$mypath = str_replace('/', '_', \dash\url::path());
				if( is_file(addons.'content_su/templates/static_'.$mypath.'.html') )
				{
					$this->display_name	= 'content_su/templates/static_'.$mypath.'.html';
				}
				// $this->display_name	= 'content_su/templates/static_'.$mypath.'.html';
				break;

			default:
				// $this->display_name	= 'content_su/templates/static_tools.html';
				return;
				break;
		}

		if($exist)
		{
			$this->model()->_processor((object) array("force_json" => false, "force_stop" => true));
		}
	}
}
?>