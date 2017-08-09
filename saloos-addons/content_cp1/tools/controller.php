<?php
namespace addons\content_cp\tools;

class controller extends \addons\content_cp\home\controller
{
	function _route()
	{
		// check permission to access to cp
		if(Tld !== 'dev')
		{
			parent::_permission('cp');
		}

		// // Restrict unwanted module
		// if(!$this->cpModlueList())
		// 	\lib\error::page(T_("Not found!"));
		$exist    = false;
		$mymodule = $this->cpModule('table');
		$cpModule = $this->cpModule('raw');

		// var_dump($this->child());
		$this->display_name	= 'content_cp/templates/raw.html';
		switch ($this->child())
		{
			case 'dbtables':
				parent::_permission('cp', 'tools', 'admin');

				$exist    = true;
				echo \lib\utility\dbTables::create();
				break;

			case 'db':
				parent::_permission('cp', 'tools', 'admin');

				\lib\db::$link_open    = [];
				\lib\db::$link_default = null;
				if(\lib\utility::post('username'))
				{
					\lib\db::$db_user = \lib\utility::post("username");
					\lib\db::$db_pass = \lib\utility::post("password");
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

				if(\lib\utility::post('type') == 'upgrade')
				{
					// do upgrade
					$result = \lib\db::install(true, true);
				}
				elseif(\lib\utility::post('type') == 'backup')
				{
					// do backup
					$result = \lib\db::backup(true);
				}
				elseif(\lib\utility::post('type') == 'backup_dump')
				{
					// do backup
					$result = \lib\db::backup_dump();
				}

				echo '<pre>';
				print_r($result);
				echo '</pre>';
				exit();
				break;


			case 'twigtrans':
				parent::_permission('cp', 'tools', 'admin');

				$exist    = true;
				$mypath   = \lib\utility::get('path');
				$myupdate = \lib\utility::get('update');
				echo \lib\utility\twigTrans::extract($mypath, $myupdate);
				break;


			case 'phpinfo':
				$exist    = true;
				phpinfo();
				break;


			case 'server':
				$exist = true;
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && !class_exists("COM"))
				{
					ob_start();
					echo "<!DOCTYPE html><meta charset='UTF-8'/><title>Extract text form twig files</title><body style='padding:0 1%;margin:0 1%;direction:ltr;overflow:hidden'>";

					echo "<h1>". T_("First you need to enable COM on windows")."</h1>";
					echo "<a target='_blank' href='http://www.php.net/manual/en/class.com.php'>" . T_("Read More") . "</a>";
					break;
				}
				\lib\utility\tools::linfo();

				$this->display_name	= 'content_cp/templates/raw-all.html';

				break;


			case 'twitter':
				$a = \lib\utility\socialNetwork::twitter('hello! test #api');
				// var_dump($a);
				break;


			case 'mergefiles':
				$exist = true;
				echo \lib\utility\tools::mergefiles('merged-project.php');
				if(\lib\utility::get('type') === 'all')
				{
					echo \lib\utility\tools::mergefiles('merged-saloos-lib.php', core.lib);
					echo \lib\utility\tools::mergefiles('merged-saloos-cp.php', addons.'content_cp/');
					echo \lib\utility\tools::mergefiles('merged-saloos-account.php', addons.'content_account/');
					echo \lib\utility\tools::mergefiles('merged-saloos-includes.php', addons.'includes/');
				}
				break;


			case 'sitemap':
				parent::_permission('cp', 'tools', 'admin');

				$exist    = true;
				$site_url = \lib\router::get_storage('url_site');
				echo "<pre>";
				echo $site_url.'<br/>';
				$sitemap  = new \lib\utility\sitemap($site_url , root.'public_html/', 'sitemap' );
				// add posts
				foreach ($this->model()->sitemap('posts', 'post') as $row)
					$sitemap->addItem($row['post_url'], '0.8', 'daily', $row['post_publishdate']);

				// add pages
				foreach ($this->model()->sitemap('posts', 'page') as $row)
					$sitemap->addItem($row['post_url'], '0.6', 'weekly', $row['post_publishdate']);

				// add helps
				foreach ($this->model()->sitemap('posts', 'helps') as $row)
					$sitemap->addItem($row['post_url'], '0.3', 'monthly', $row['post_publishdate']);

				// add attachments
				foreach ($this->model()->sitemap('posts', 'attachment') as $row)
					$sitemap->addItem($row['post_url'], '0.2', 'weekly', $row['post_publishdate']);

				// add other type of post
				foreach ($this->model()->sitemap('posts', false) as $row)
					$sitemap->addItem($row['post_url'], '0.5', 'weekly', $row['post_publishdate']);

				// add cats and tags
				foreach ($this->model()->sitemap('terms') as $row)
					$sitemap->addItem($row['term_url'], '0.4', 'weekly', $row['date_modified']);

				$sitemap->createSitemapIndex();
				echo "</pre>";
				echo "<p class='alert alert-success'>". T_('Create sitemap Successfully!')."</p>";


				// echo "Create Successful";
				break;


			case 'git':
				parent::_permission('cp', 'tools', 'admin');

				// declare variables
				$exist    = true;
				$rep      = null;
				$result   = [];
				$location = '../../';
				$name     = \lib\utility::get('name');
				$output   = null;

				// switch by name of repository
				switch ($name)
				{
					case 'saloos':
						$location .= 'saloos';
						$rep      .= "https://github.com/Ermile/Saloos.git";
						break;

					case 'addons':
						$location .= 'saloos/saloos-addons';
						$rep      .= "https://github.com/Ermile/Saloos-Addons.git";
						break;

					default:
						$location .= $name;
						// $exist = false;
						// return;
						break;
				}
				// change location to address of requested
				chdir($location);
				// start show result
				$output   = "<pre>";
				$output  .= 'Repository address: '. getcwd(). '<br/>';
				$output  .= 'Remote address:     '. $location. '<hr/>';
				// $command  = 'git pull '.$rep.' 2>&1';
				$command  = 'git pull origin master 2>&1';


				// Print the exec output inside of a pre element
				exec($command, $result);
				if(!$result)
				{
					$output .= T_('Not Work!');
				}
				foreach ($result as $line)
				{
					$output .= $line . "\n";
				}
				$output .= "</pre>";

				echo $output;
				break;


			case 'log':
				parent::_permission('cp', 'tools', 'view');
				$exist      = true;
				$output     = '<html>';
				$name       = \lib\utility::get('name');
				$isClear    = \lib\utility::get('clear');
				$isZip      = \lib\utility::get('zip');
				$clearName  = '';
				$clearURL   = '';
				$page       = \lib\utility::get('p') * 100000;
				if($page< 0)
				{
					$page = 0;
				}
				$lenght      = \lib\utility::get('lenght');
				if($lenght< 100000)
				{
					$lenght = 100100;
				}
				$filepath   = '';
				$fileFormat = 'sql';


				switch ($name)
				{
					case 'accesslog':
						$clearName = 'log_access_bak_' .date("Ymd_His");
						$clearExt  = '.sql';
						$clearURL = database.'log/backup-log/'. $clearName. $clearExt;
						$filepath = '/var/log/apache2/access.log';
						$lang     = 'sql';
						break;

					case 'errorlog':
						$clearName = 'log_erro_bak_' .date("Ymd_His");
						$clearExt  = '.sql';
						$clearURL = database.'log/backup-log/'. $clearName. $clearExt;
						$filepath = '/var/log/apache2/error.log';
						$lang     = 'sql';
						break;

					case 'sql':
						$clearName = 'log_bak_' .date("Ymd_His");
						$clearExt  = '.sql';
						$clearURL = database.'log/backup-db/'. $clearName. $clearExt;
						$filepath = database.'log/log.sql';
						$lang     = 'sql';
						break;

					case 'sql_check':
						$clearName = 'log_check_bak_' .date("Ymd_His");
						$clearExt  = '.sql';
						$clearURL = database.'log/backup-db/'. $clearName. $clearExt;
						$filepath = database.'log/log-check.sql';
						$lang     = 'sql';
						break;

					case 'sql_warn':
						$clearName = 'log_warn_bak_' .date("Ymd_His");
						$clearExt  = '.sql';
						$clearURL = database.'log/backup-db/'. $clearName. $clearExt;
						$filepath = database.'log/log-warn.sql';
						$lang     = 'sql';
						break;

					case 'sql_critical':
						$clearName = 'log_critical_bak_' .date("Ymd_His");
						$clearExt  = '.sql';
						$clearURL = database.'log/backup-db/'. $clearName. $clearExt;
						$filepath = database.'log/log-critical.sql';
						$lang     = 'sql';
						break;

					case 'sql_error':
						$clearName = 'error_bak_' .date("Ymd_His");
						$clearExt  = '.sql';
						$clearURL = database.'log/backup-db/'. $clearName. $clearExt;
						$filepath = database.'log/error.sql';
						$lang     = 'sql';
						break;

					case 'telegram':
						$clearName = 'telegram_bak_' .date("Ymd_His");
						$clearExt  = '.json';
						$clearURL = database.'log/backup-tg/'. $clearName. $clearExt;
						$filepath = database.'log/telegram.json';
						$lang     = 'json';
						break;

					case 'telegram_info':
						$clearName = 'telegram_info_bak_' .date("Ymd_His");
						$clearExt  = '.json';
						$clearURL = database.'log/backup-tg/'. $clearName. $clearExt;
						$filepath = database.'log/telegram-info.json';
						$lang     = 'json';
						break;

					case 'telegram_error':
						$clearName = 'telegram_error_bak_' .date("Ymd_His");
						$clearExt  = '.json';
						$clearURL = database.'log/backup-tg/'. $clearName. $clearExt;
						$filepath = database.'log/telegram-error.json';
						$lang     = 'json';
						break;

					default:
						$output .= T_('Do you wanna something here!?');
						break;
				}
				// if wanna clear this file, transfer it to new address and clear it
				if($isClear)
				{
					\lib\utility\file::rename($filepath, $clearURL);
					$this->redirector('?name='. $name)->redirect();
				}
				if($isZip)
				{
					$newZipAddr = database.'log/dl.zip';
					// create zip
					if(\lib\utility\zip::create($filepath, $newZipAddr) === true)
					{
						\lib\utility\zip::download_on_fly($newZipAddr, $clearName);
					}
				}

				// read file data
				$fileData = @file_get_contents($filepath, FILE_USE_INCLUDE_PATH, null, $page, $lenght);
				if($fileData)
				{
					$myURL    = Protocol."://". \lib\router::get_root_domain().'/static';
					$myCommon = Protocol."://ermile.".Tld.'/static/js/common.js';
					$output .= "<head>";
					$output .= ' <title>Log | '. $name. '</title>';
					$output .= ' <script src="'. $myCommon. '"></script>';
					$output .= ' <script src="'. $myURL. '/js/lib/highlight/highlight.min.js"></script>';
					$output .= ' <link rel="stylesheet" href="'. $myURL. '/css/lib/highlight/atom-one-dark.css">';
					$output .= ' <style>';
					$output .= 'body{margin:0;height:100%;} .clear{position:absolute;top:1em;right:2em;border:1px solid #fff;color:#fff;border-radius:3px;padding:0.5em 1em;text-decoration:none} .zip{position:absolute;bottom:1.5em;right:2em;background-color:#000;color:#fff;border-radius:3px;padding:0.5em 1em;text-decoration:none} .hljs{padding:0;max-height:100%;height:100%;}';
					$output .= ' </style>';

					$output .= ' <script>$(document).ready(function() {$("pre").each(function(i, block) {hljs.highlightBlock(block);}); });</script>';
					$output .= "</head><body>";
					$output .= '<a class="clear" href="?name='. $name. '&clear=true">Clear it!</a>';
					$output .= '<a class="zip" href="?name='. $name. '&zip=true">ZIP it!</a>';
					$output .= "<pre class='$lang'>";
					$output .= $fileData;
					$output .= "</pre>";
				}
				else
				{
					$output .= 'File does not exist!';
				}

				$output .= "</body></html>";
				echo $output;
				break;


			case null:
				$mypath = $this->url('path','_');
				if( is_file(addons.'content_cp/templates/static_'.$mypath.'.html') )
				{
					$this->display_name	= 'content_cp/templates/static_'.$mypath.'.html';
				}
				// $this->display_name	= 'content_cp/templates/static_'.$mypath.'.html';
				break;

			default:
				$this->display_name	= 'content_cp/templates/static_tools.html';

				return;
				break;
		}

		// $this->get()->ALL();
		if($exist)
		{
			$this->model()->_processor(object(array("force_json" => false, "force_stop" => true)));
		}

		return;


	}
}
?>