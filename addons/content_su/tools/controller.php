<?php
namespace addons\content_su\tools;

class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();

		$exist    = false;

		$url_child = \lib\url::dir(1);

		switch ($url_child)
		{
			case 'dbtables':
				// 	$exist    = true;
				// 	echo \lib\utility\dbTables::create();
				// 	break;

			case 'db':

				\lib\db::$link_open    = [];
				\lib\db::$link_default = null;
				if(\lib\request::post('username'))
				{
					\lib\db::$db_user = \lib\request::post("username");
					\lib\db::$db_pass = \lib\request::post("password");
				}
				elseif(defined('admin_db_user') && defined('admin_db_pass'))
				{
					\lib\db::$db_user = constant("admin_db_user");
					\lib\db::$db_pass = constant("admin_db_pass");
				}
				elseif(defined('db_user') && defined('db_pass'))
				{
					\lib\db::$db_user = constant("db_user");
					\lib\db::$db_pass = constant("db_pass");
				}
				else
				{
					\lib\error::access(T_("Permission denide for run upgrade database"));
				}

				\lib\db::$debug_error = false;

				$result = null;
				$exist  = true;

				if(\lib\request::post('type') == 'upgrade')
				{
					// do upgrade
					$result = \lib\db::install(true, true);
				}
				elseif(\lib\request::post('type') == 'backup')
				{
					// do backup
					$result = \lib\db::backup(true);
				}
				elseif(\lib\request::post('type') == 'backup_dump')
				{
					// do backup
					$result = \lib\db::backup_dump();
				}

				\lib\code::print($result, true);
				\lib\code::exit();
				break;


			case 'twitter':
				$a = \lib\utility\socialNetwork::twitter('hello! test #api');
				break;

			case 'mergefiles':
				// 	$exist = true;
				// 	echo \lib\utility\tools::mergefiles('merged-project.php');
				// 	if(\lib\request::get('type') === 'all')
				// 	{
				// 		echo \lib\utility\tools::mergefiles('merged-lib.php', core.lib);
				// 		echo \lib\utility\tools::mergefiles('merged-su.php', addons.'content_su/');
				// 		echo \lib\utility\tools::mergefiles('merged-account.php', addons.'content_account/');
				// 		echo \lib\utility\tools::mergefiles('merged-includes.php', addons.'includes/');
				// 	}
				// 	break;

			case null:
				$mypath = str_replace('/', '_', \lib\url::path());
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