<?php
namespace lib;

class view
{

	/**
	 * twig options
	 * @var array
	 */
	public static $data    = [];
	public static $url     = [];
	public static $global  = [];
	public static $include = [];


	public static function start()
	{
		self::variable();
		self::twig();
	}


	public static function variable()
	{

		self::$data                  = (object) [];
		self::$data->url             = (object) [];
		self::$data->include         = (object) [];
		self::$data->global          = (object) [];
		// self::$url                   = self::$data->url;
		self::$global                = self::$data->global;
		self::$include               = self::$data->include;

		// default display value
		self::$data->display['mvc']        = "includes/html/display-mvc.html";
		self::$data->display['dash']       = "includes/html/display-dash.html";
		self::$data->display['enter']      = "includes/html/display-enter.html";
		// add special pages to display array to use without name
		self::$data->display['main']       = "content/main/layout.html";
		self::$data->display['home']       = "content/home/display.html";
		self::$data->display['account']    = "content_account/home/layout.html";
		self::$data->display['cp']         = "content_cp/home/layout.html";
		self::$data->display['su']         = "content_su/home/layout.html";
		self::$data->display['cpMain']     = "content_cp/main/layout.html";
		self::$data->display['suMain']     = "content_su/main/layout.html";
		self::$data->display['pagination'] = "includes/html/inc_pagination.html";
		// add special pages to template array to use without name
		self::$data->template['header']    = 'content/template/header.html';
		self::$data->template['sidebar']   = 'content/template/sidebar.html';
		self::$data->template['footer']    = 'content/template/footer.html';

		// set url values
		self::$url       = \lib\url::all();
		self::$data->url = self::$url;

		// return all parameters and clean it
		self::$data->requestGET = \lib\request::get(null, 'raw');

		// ----- language variable
		self::$data->lang            = [];
		self::$data->lang['list']    = \lib\language::list(true);
		self::$data->lang['current'] = \lib\language::current();
		self::$data->lang['default'] = \lib\language::default();

		// save all options to use in display
		self::$data->options = \lib\option::config();

		self::$data->page['title']   = null;
		self::$data->page['desc']    = null;
		self::$data->page['special'] = null;
		self::$data->bodyclass       = null;

		self::$data->user = self::$data->login  = \lib\user::detail();

		// set detail of browser
		self::$data->browser         = \lib\utility\browserDetection::browser_detection('full_assoc');
		self::$data->visitor         = 'not ready!';

		// define default value for global
		self::$global->title         = null;
		self::$global->login         = \lib\user::login();
		self::$global->lang          = self::$data->lang['current'];
		self::$global->direction     = \lib\language::current('direction');
		self::$global->id            = implode('_', \lib\url::dir());

		self::$data->dev = \lib\option::config('dev');

		self::$data->site['title']       = T_("Ermile Dash");
		self::$data->site['desc']        = T_("Another Project with Ermile dash");
		self::$data->site['slogan']      = T_("Ermile is intelligent ;)");

		// if allow to use social then get social network account list
		if(\lib\option::social('status'))
		{
			self::$data->social = \lib\option::social('list');
			// create data of share url
			self::$data->share['title']       = self::$data->site['title'];
			self::$data->share['desc']        = self::$data->site['desc'];
			self::$data->share['image']       = \lib\url::site(). '/static/images/logo.png';
			self::$data->share['twitterCard'] = 'summary';
		}

		// define default value for include
		self::$include->siftal       = true;
		self::$include->css          = true;
		self::$include->js           = true;
		self::set_title();
	}


	public static function twig()
	{
		self::$data->loadMode = 'normal';
		if(\lib\request::ajax())
		{
			self::$data->display['dash']    = "includes/html/display-dash-xhr.html";
			self::$data->display['enter']   = "includes/html/display-enter-xhr.html";

			self::$data->display['main']    = "content/main/layout-xhr.html";
			self::$data->display['home']    = "content/home/display-xhr.html";
			self::$data->display['account'] = "content_account/home/layout-xhr.html";
			self::$data->loadMode           = 'ajax';
		}
		$module       = preg_replace("/^[
			^\/]*\/?content/", "content", \lib\engine\mvc::get_dir_address());
		$module       = preg_replace("/^content\\\\|(model|view|controller)$/", "", $module);
		$module       = preg_replace("/[\\\]/", "/", $module);
		// $repository   = \lib\engine\content::get();
		// $repository   = $repository ==='content'? $repository.'/': null;
		// $tmpname      = (self::$controller()->display_name)? self::$controller()->display_name : $repository.'/'.$module.'display.html';
		// $tmpname      = $repository.$module.'/display.html';
		$tmpname      = $module.'/display.html';

		if(\lib\url::content() === null)
		{
			self::$data->datarow = \lib\app\template::$datarow;
			self::set_cms_titles();
		}

		self::$data->pagination = \lib\utility\pagination::page_number();

		// ************************************************************************************ Twig
		// twig method

		require_once core.'addons/lib/Twig/lib/Twig/Autoloader.php';
		\Twig_Autoloader::register();

		$twig_include_path     = [];
		$twig_include_path[]   = root;
		$twig_include_path[]   = addons;
		$loader                = new \Twig_Loader_Filesystem($twig_include_path);
		$array_option          = [];
		$array_option['debug'] = true;

		$twig = new \Twig_Environment($loader, $array_option);

		\lib\engine\twigAddons::init($twig);

		$twig->addGlobal("session", $_SESSION);

		if(\lib\engine\dev::debug())
		{
			$twig->addExtension(new \Twig_Extension_Debug());
		}

		$twig->addExtension(new \Twig_Extensions_Extension_I18n());

		$template		= $twig->loadTemplate($tmpname);

		if(\lib\request::ajax())
		{
			self::$data->global->debug = \lib\notif::get();
			// check apache request header and use if exist
			if(function_exists('apache_request_headers'))
			{
				$req = apache_request_headers();
			}

			$xhr_render                    = $template->render((array) self::$data);
			// self::$data->display['mvc'] = self::$data->display['xhr'];
			$md5                           = md5(json_encode(self::$data->global).$xhr_render);
			if(isset($req['Cached-MD5']) && $req['Cached-MD5'] == $md5)
			{
				echo json_encode(array("getFromCache" => true));
			}
			else
			{
				// self::$data->global->md5 = $md5;
				echo json_encode(self::$data->global);
				echo "\n";
				echo $xhr_render;
			}
		}
		else
		{
			$template->display((array) self::$data);
		}
	}


	/**
	 * set title for pages depending on condition
	 */
	public static function set_title()
	{
		if($page_title = self::$data->page['title'])
		{
			// set title of locations if exist in breadcrumb
			if(isset(self::$data->breadcrumb[$page_title]))
			{
				$page_title = self::$data->breadcrumb[$page_title];
			}
			// replace title of page
			if(!self::$data->page['special'])
			{
				$page_title = ucwords(str_replace('-', ' ', $page_title));
			}
			// for child page set the
			if(\lib\url::child() && \lib\url::subdomain() === 'cp')
			{
				$myModule = \lib\url::module();
				if(substr($myModule, -3) === 'ies')
				{
					$moduleName = substr($myModule, 0, -3).'y';
				}
				elseif(substr($myModule, -1) === 's')
				{
					$moduleName = substr($myModule, 0, -1);
				}
				else
				{
					$moduleName = $myModule;
				}

				$childName = \lib\url::child();
				if($childName)
				{
					$page_title = T_($childName).' '.T_($moduleName);
				}
			}

			// translate all title at last step
			$page_title = T_($page_title);
			self::$data->page['title'] = $page_title;
			if(self::$data->page['special'])
			{
				self::$global->title = $page_title;
			}
			else
			{
				self::$global->title = $page_title.' | '.T_(self::$data->site['title']);
			}
		}
		else
		{
			self::$global->title = T_(self::$data->site['title']);
		}

		self::$global->short_title = substr(self::$global->title, 0, strrpos(substr(self::$global->title, 0, 120), ' ')) . '...';
	}


	private static function set_cms_titles()
	{
		if(!self::$data->datarow)
		{
			return false;
		}

		// set title
		if(isset(self::$data->datarow['title']))
		{
			self::$data->page['title'] = self::$data->datarow['title'];
		}

		// set desc
		if(isset(self::$data->datarow['excerpt']) && self::$data->datarow['excerpt'])
		{
			self::$data->page['desc'] = self::$data->datarow['excerpt'];
		}
		elseif(isset(self::$data->datarow['content']) && self::$data->datarow['content'])
		{
			self::$data->page['desc'] = \lib\utility\excerpt::extractRelevant(self::$data->datarow['content']);
		}
		elseif(isset(self::$data->datarow['desc']) && self::$data->datarow['desc'])
		{
			self::$data->page['desc'] = \lib\utility\excerpt::extractRelevant(self::$data->datarow['desc']);
		}

		// set new title
		self::$set_title();
	}
}
?>
