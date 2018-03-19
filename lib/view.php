<?php
namespace lib;

class view
{
	use \lib\mvc;
	use \lib\utility\twigAddons;

	/**
	 * twig options
	 * @var array
	 */
	public $twig = [];


	public function __construct($_startObject = false)
	{
		if(!$_startObject)
		{
			return;
		}

		$this->controller            = $_startObject->controller;

		$this->data                  = (object) [];
		$this->data->url             = (object) [];
		$this->data->include         = (object) [];
		$this->data->global          = (object) [];
		$this->url                   = $this->data->url;
		$this->global                = $this->data->global;
		$this->include               = $this->data->include;

		// default display value
		$this->data->display['mvc']        = "includes/html/display-mvc.html";
		$this->data->display['dash']       = "includes/html/display-dash.html";
		$this->data->display['enter']      = "includes/html/display-enter.html";
		// add special pages to display array to use without name
		$this->data->display['main']       = "content/main/layout.html";
		$this->data->display['home']       = "content/home/display.html";
		$this->data->display['account']    = "content_account/home/layout.html";
		$this->data->display['cp']         = "content_cp/home/layout.html";
		$this->data->display['su']         = "content_su/home/layout.html";
		$this->data->display['cpMain']     = "content_cp/main/layout.html";
		$this->data->display['suMain']     = "content_su/main/layout.html";
		$this->data->display['pagination'] = "includes/html/inc_pagination.html";
		// add special pages to template array to use without name
		$this->data->template['header']    = 'content/template/header.html';
		$this->data->template['sidebar']   = 'content/template/sidebar.html';
		$this->data->template['footer']    = 'content/template/footer.html';

		// set url values
		$this->url               = \lib\url::all();
		$this->url['static']     = \lib\url::site(). '/static/';
		$this->url['repository'] = 'site';
		if(\lib\url::content())
		{
			$this->url['repository'] = \lib\url::content();
		}

		$this->data->url = $this->url;

		// return all parameters and clean it
		$this->data->requestGET = \lib\request::get(null, 'raw');

		// ----- language variable
		$this->data->lang            = [];
		$this->data->lang['list']    = \lib\language::list(true);
		$this->data->lang['current'] = \lib\language::current();
		$this->data->lang['default'] = \lib\language::default();

		// save all options to use in display
		$this->data->options = \lib\option::config();

		$this->data->page['title']   = null;
		$this->data->page['desc']    = null;
		$this->data->page['special'] = null;
		$this->data->bodyclass       = null;

		$this->data->user = $this->data->login  = \lib\user::detail();

		// set detail of browser
		$this->data->browser         = \lib\utility\browserDetection::browser_detection('full_assoc');
		$this->data->visitor         = 'not ready!';

		// define default value for global
		$this->global->title         = null;
		$this->global->login         = \lib\user::login();
		$this->global->lang          = $this->data->lang['current'];
		$this->global->direction     = \lib\language::current('direction');
		$this->global->id            = implode('_', \lib\url::dir());

		$this->data->dev = \lib\option::config('dev');

		$this->data->site['title']       = T_("Ermile Dash");
		$this->data->site['desc']        = T_("Another Project with Ermile dash");
		$this->data->site['slogan']      = T_("Ermile is intelligent ;)");

		// if allow to use social then get social network account list
		if(\lib\option::social('status'))
		{
			$this->data->social = \lib\option::social('list');
			// create data of share url
			$this->data->share['title']       = $this->data->site['title'];
			$this->data->share['desc']        = $this->data->site['desc'];
			$this->data->share['image']       = $this->url['static']. 'images/logo.png';
			$this->data->share['twitterCard'] = 'summary';
		}

		// define default value for include
		$this->include->newline      = PHP_EOL;
		$this->include->css_ermile   = false;
		$this->include->js_main      = false;
		$this->include->siftal       = true;
		$this->include->css          = true;
		$this->include->js           = true;

		// we offer 3 type of function to be used in order to have some change on module
		// you can call this all time needed, but Recomended to call on project mvc view
		if(method_exists($this, 'project'))
		{
			$this->project();
		}
		// like project but recomend to call on repository
		if(method_exists($this, 'repository'))
		{
			$this->repository();
		}
		// like project but recomend to call on special module
		if(method_exists($this, 'config'))
		{
			$this->config();
		}
	}


	/**
	 * if controller display property was true run this function for display module
	 */
	public function corridor()
	{
		// if set title exist
		if(method_exists($this, 'set_title'))
		{
			$this->set_title();
		}
		$this->display();
	}

	public function display()
	{
		$render               = false;
		$this->data->loadMode = 'normal';
		if(\lib\request::ajax())
		{
			$this->data->display['dash']    = "includes/html/display-dash-xhr.html";
			$this->data->display['enter']   = "includes/html/display-enter-xhr.html";

			$this->data->display['main']    = "content/main/layout-xhr.html";
			$this->data->display['home']    = "content/home/display-xhr.html";
			$this->data->display['account'] = "content_account/home/layout-xhr.html";
			$this->data->loadMode           = 'ajax';
		}
		$module       = preg_replace("/^[^\/]*\/?content/", "content", get_class($this->controller));
		$module       = preg_replace("/^content\\\\|(model|view|controller)$/", "", $module);
		$module       = preg_replace("/[\\\]/", "/", $module);
		$repository   = \lib\engine\content::get();
		$repository   = $repository ==='content'? $repository.'/': null;
		// $tmpname      = ($this->controller()->display_name)? $this->controller()->display_name : $repository.'/'.$module.'display.html';
		$tmpname      = ($this->controller()->display_name)? $this->controller()->display_name : $repository.$module.'display.html';

		if(\lib\url::content() === null)
		{
			$this->data->datarow = \lib\app\template::$datarow;
			self::set_cms_titles();
		}

		$this->data->pagination = \lib\utility\pagination::page_number();

		// ************************************************************************************ Twig
		// twig method
		$this->add_twig_filter('fcache');
		$this->add_twig_filter('jdate');
		$this->add_twig_filter('tdate');
		$this->add_twig_filter('sdate');
		$this->add_twig_filter('readableSize');
		$this->add_twig_filter('persian');
		$this->add_twig_filter('fitNumber');
		$this->add_twig_filter('humantime');
		$this->add_twig_filter('exist');
		$this->add_twig_filter('decode');
		$this->add_twig_filter('coding');
		$this->add_twig_filter('filemtime');
		$this->add_twig_function('breadcrumb');
		$this->add_twig_function('langList');
		$this->add_twig_function('posts');
		$this->add_twig_function('tags');
		$this->add_twig_function('category');
		$this->add_twig_function('comments');
		$this->add_twig_function('similar_post');
		$this->add_twig_function('attachment');
		$this->add_twig_function('post_search');
		$this->add_twig_function('perm');
		$this->add_twig_function('perm_su');

		require_once core.'addons/lib/Twig/lib/Twig/Autoloader.php';
		\Twig_Autoloader::register();

		$twig_include_path   = [];
		$twig_include_path[] = root;
		$twig_include_path[] = addons;
		$loader              = new \Twig_Loader_Filesystem($twig_include_path);
		$array_option        = [];

		if($this->controller()->debug())
		{
			$array_option['debug'] = true;
		}

		// twig var_dump filter for dumping value
		$filter_dump       = new \Twig_SimpleFilter('dump', 'var_dump');
		// Delete a key of an array
		$filter_unset_type = new \Twig_SimpleFilter('unset_type', function ($array= null)
		{
			unset($array['attr']['type']);
			return $array;
		});

		$twig		          = new \Twig_Environment($loader, $array_option);
		$twig->addFilter($filter_dump);						// add a new filter to twig
		$twig->addFilter($filter_unset_type);				// add a new filter to twig
		$twig->addGlobal("session", $_SESSION);

		if($this->controller()->debug())
		{
			$twig->addExtension(new \Twig_Extension_Debug());
		}
		else
		{
			$this->add_twig_function('dump');
		}

		$twig->addExtension(new \Twig_Extensions_Extension_I18n());

		$this->twig_Extentions($twig);
		$template		= $twig->loadTemplate($tmpname);
		if(\lib\request::ajax())
		{
			$this->data->global->debug = \lib\notif::get();
			// check apache request header and use if exist
			if(function_exists('apache_request_headers'))
			{
				$req = apache_request_headers();
			}

			$xhr_render                 = $template->render((array) $this->data);
			// $this->data->display['mvc'] = $this->data->display['xhr'];
			$md5                        = md5(json_encode($this->data->global).$xhr_render);
			if(isset($req['Cached-MD5']) && $req['Cached-MD5'] == $md5)
			{
				echo json_encode(array("getFromCache" => true));
			}
			else
			{
				// $this->data->global->md5 = $md5;
				echo json_encode($this->data->global);
				echo "\n";
				echo $xhr_render;
			}
		}
		else
		{
			$template->display((array) $this->data);
		}
	}


	/**
	 * set title for pages depending on condition
	 */
	public function set_title()
	{
		if($page_title = $this->data->page['title'])
		{
			// set title of locations if exist in breadcrumb
			if(isset($this->data->breadcrumb[$page_title]))
			{
				$page_title = $this->data->breadcrumb[$page_title];
			}
			// replace title of page
			if(!$this->data->page['special'])
			{
				$page_title = ucwords(str_replace('-', ' ', $page_title));
			}
			// for child page set the
			if($this->url['child'] && \lib\url::subdomain() === 'cp')
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
			$this->data->page['title'] = $page_title;
			if($this->data->page['special'])
			{
				$this->global->title = $page_title;
			}
			else
			{
				$this->global->title = $page_title.' | '.T_($this->data->site['title']);
			}
		}
		else
		{
			$this->global->title = T_($this->data->site['title']);
		}

		$this->global->short_title = substr($this->global->title, 0, strrpos(substr($this->global->title, 0, 120), ' ')) . '...';
	}


	private function set_cms_titles()
	{
		if(!$this->data->datarow)
		{
			return false;
		}

		// set title
		if(isset($this->data->datarow['title']))
		{
			$this->data->page['title'] = $this->data->datarow['title'];
		}

		// set desc
		if(isset($this->data->datarow['excerpt']) && $this->data->datarow['excerpt'])
		{
			$this->data->page['desc'] = $this->data->datarow['excerpt'];
		}
		elseif(isset($this->data->datarow['content']) && $this->data->datarow['content'])
		{
			$this->data->page['desc'] = \lib\utility\excerpt::extractRelevant($this->data->datarow['content']);
		}
		elseif(isset($this->data->datarow['desc']) && $this->data->datarow['desc'])
		{
			$this->data->page['desc'] = \lib\utility\excerpt::extractRelevant($this->data->datarow['desc']);
		}

		// set new title
		$this->set_title();
	}
}
?>
