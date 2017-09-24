<?php
namespace lib;

class view
{
	use mvc;

	use view\twigAddons;
	use view\constructor;


	/**
	 * twig options
	 * @var array
	 */
	public $twig = array();
	/**
	 * constructor
	 * @param boolean $object controller
	 */

	public $twig_include_path = array();

	public function __construct($object = false)
	{
		if(!$object) return;

		$this->controller            = $object->controller;

		$this->data                  = new view\data();
		$this->data->url             = (object) [];
		$this->data->include         = (object) [];
		$this->data->global          = (object) [];
		$this->url                   = $this->data->url;
		$this->global                = $this->data->global;
		$this->include               = $this->data->include;

		// default data property
		$this->data->macro['forms']   = 'includes/macro/forms.html';
		// default display value
		$this->data->display['mvc']   = "includes/html/display-mvc.html";
		$this->data->display['dash']  = "includes/html/display-dash.html";
		$this->data->display['enter'] = "includes/html/display-enter.html";


		$myurl = router::get_protocol().'://'.router::get_domain().$_SERVER['REQUEST_URI'];
		if( isset($_SERVER['HTTP_REFERER']) && isset($_SESSION['debug'][md5($_SERVER['HTTP_REFERER'])]) )
		{
			$myurl = $_SERVER['HTTP_REFERER'];
		}

		array_push($this->twig_include_path, root);

		if(method_exists($this, 'mvc_construct'))
		{
			$this->mvc_construct();
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
		if(\lib\dash::is_Ajax())
		{
			$this->data->display['dash']    = "includes/html/display-dash-xhr.html";
			$this->data->display['enter']   = "includes/html/display-enter-xhr.html";

			$this->data->display['main']    = "content/main/layout-xhr.html";
			$this->data->display['home']    = "content/home/display-xhr.html";
			$this->data->display['account'] = "content_account/home/layout-xhr.html";
			$this->data->loadMode           = 'ajax';

			if($this->method_exists("pushState"))
			{
				$this->ipushState();
			}
		}
		$module       = preg_replace("/^[^\/]*\/?content/", "content", get_class($this->controller));
		$module       = preg_replace("/^content\\\\|(model|view|controller)$/", "", $module);
		$module       = preg_replace("/[\\\]/", "/", $module);
		$a_repository = preg_split("/[\/]/", router::get_repository(), -1, PREG_SPLIT_NO_EMPTY);
		$repository   = end($a_repository);
		$repository   = $repository ==='content'? $repository.'/': null;
		// $tmpname      = ($this->controller()->display_name)? $this->controller()->display_name : $repository.'/'.$module.'display.html';
		$tmpname      = ($this->controller()->display_name)? $this->controller()->display_name : $repository.$module.'display.html';


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
		$this->add_twig_filter('shortURL');
		$this->add_twig_filter('filemtime');
		$this->add_twig_function('breadcrumb');
		$this->add_twig_function('langList');
		$this->add_twig_function('posts');
		$this->add_twig_function('tags');
		$this->add_twig_function('comments');
		$this->add_twig_function('similar_post');
		$this->add_twig_function('attachment');
		$this->add_twig_function('post_search');
		$this->add_twig_function('perm');
		$this->add_twig_function('perm_su');

		require_once core.'addons/lib/Twig/lib/Twig/Autoloader.php';
		\Twig_Autoloader::register();
		$loader		  = new \Twig_Loader_Filesystem($this->twig_include_path);
		$array_option = array();
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
		if(\lib\dash::is_Ajax())
		{
			$this->data->global->debug = \lib\debug::compile();
			// check apache request header and use if exist
			if(function_exists('apache_request_headers'))
			{
				$req = apache_request_headers();
			}

			$xhr_render                 = $template->render($this->data->_toArray());
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
			$template->display($this->data->_toArray());
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
			if($this->data->child && SubDomain === 'cp')
			{
				if(substr($this->module(), -3) === 'ies')
				{
					$moduleName = substr($this->module(), 0, -3).'y';
				}
				elseif(substr($this->module(), -1) === 's')
				{
					$moduleName = substr($this->module(), 0, -1);
				}
				else
				{
					$moduleName = $this->module();
				}

				$childName = $this->child(true);
				if($childName)
				{
					$page_title = T_($childName).' '.T_($moduleName);
				}
			}

			// set user-friendly title for books
			if($this->module() === 'book')
			{
				$breadcrumb = $this->model()->breadcrumb();
				$page_title = $breadcrumb[0] . ' ';
				array_shift($breadcrumb);

				foreach ($breadcrumb as $value)
				{
					$page_title .= $value . ' - ';
				}
				$page_title = substr($page_title, 0, -3);
				$this->data->parentList = $this->model()->sp_books_nav();
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
}
?>
