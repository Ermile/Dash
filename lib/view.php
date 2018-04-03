<?php
namespace lib;

class view
{

	public static function start()
	{
		self::variable();
		self::twig();
	}


	public static function variable()
	{
		// default display value
		\lib\data::display("includes/html/display-mvc.html", 			'mvc');
		\lib\data::display("includes/html/display-dash.html", 			'dash');
		\lib\data::display("includes/html/display-enter.html", 			'enter');
		// add special pages to display array to use without name
		\lib\data::display("content/main/layout.html", 					'main');
		\lib\data::display("content/home/display.html", 				'home');
		\lib\data::display("content_account/home/layout.html", 			'account');
		\lib\data::display("content_cp/home/layout.html", 				'cp');
		\lib\data::display("content_su/home/layout.html", 				'su');
		\lib\data::display("content_cp/main/layout.html", 				'cpMain');
		\lib\data::display("content_su/main/layout.html", 				'suMain');

		\lib\data::display("includes/html/inc_pagination.html", 		'pagination');

		// return all url detail
		\lib\data::url(\lib\url::all());

		// return all parameters and clean it
		\lib\data::requestGET(\lib\request::get(null, 'raw'));

		// ----- language variable
		\lib\data::lang(\lib\language::list(true),  'list');
		\lib\data::lang(\lib\language::current(),   'current');
		\lib\data::lang(\lib\language::default(),   'default');

		// save all options to use in display
		\lib\data::options(\lib\option::config());

		\lib\data::page(null, 'title');
		\lib\data::page(null, 'desc');
		\lib\data::page(null, 'special');

		\lib\data::bodyclass(null);

		$user_detail = \lib\user::detail();
		\lib\data::user($user_detail);
		\lib\data::login($user_detail);

		// set detail of browser
		\lib\data::browser(\lib\utility\browserDetection::browser_detection('full_assoc'));
		\lib\data::visitor('not ready!');

		// define default value for global
		\lib\data::global(null, 								'title');
		\lib\data::global(\lib\user::login(), 					'login');
		\lib\data::global(\lib\language::current(), 	 		'lang');
		\lib\data::global(\lib\language::current('direction'), 	'direction');
		\lib\data::global(implode('_', \lib\url::dir()), 		'id');

		\lib\data::dev(\lib\option::config('dev'));

		\lib\data::site(T_("Ermile Dash"), 						'title');
		\lib\data::site(T_("Another Project with Ermile dash"), 'desc');
		\lib\data::site(T_("Ermile is intelligent ;)"), 		'slogan');

		// if allow to use social then get social network account list
		if(\lib\option::social('status'))
		{
			\lib\data::social(\lib\option::social('list'));
			// create data of share url
			\lib\data::share(\lib\data::get('site', 'title'), 				'title');
			\lib\data::share(\lib\data::get('site', 'desc'), 				'desc');
			\lib\data::share(\lib\url::site(). '/static/images/logo.png', 	'image');
			\lib\data::share('summary', 									'twitterCard');
		}

		// define default value for include
		\lib\data::include(true, 'siftal');
		\lib\data::include(true, 'css');
		\lib\data::include(true, 'js');

		self::set_title();
	}


	public static function twig()
	{
		\lib\data::loadMode('normal');
		if(\lib\request::ajax())
		{
			\lib\data::display("includes/html/display-dash-xhr.html", 	'dash');
			\lib\data::display("includes/html/display-enter-xhr.html", 	'enter');
			\lib\data::display("content/main/layout-xhr.html", 			'main');
			\lib\data::display("content/home/display-xhr.html", 		'home');
			\lib\data::display("content_account/home/layout-xhr.html", 	'account');
			\lib\data::loadMode('ajax');
		}

		$module       = preg_replace("/^[
			^\/]*\/?content/", "content", \lib\engine\mvc::get_dir_address());
		$module  = preg_replace("/^content\\\\|(model|view|controller)$/", "", $module);
		$module  = preg_replace("/[\\\]/", "/", $module);
		$tmpname = $module.'/display.html';

		if(\lib\url::content() === null)
		{
			\lib\data::datarow(\lib\app\template::$datarow);
			self::set_cms_titles();
		}

		\lib\data::pagination(\lib\utility\pagination::page_number());

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

		$template = $twig->loadTemplate($tmpname);

		if(\lib\request::ajax())
		{
			\lib\data::global(\lib\notif::get(), 'debug');
			$xhr_render                = $template->render(\lib\data::get());

			echo json_encode(\lib\data::get('global'));
			echo "\n";
			echo $xhr_render;
		}
		else
		{
			$template->display(\lib\data::get());
		}
	}


	/**
	 * set title for pages depending on condition
	 */
	public static function set_title()
	{
		if($page_title = \lib\data::get('page', 'title'))
		{
			// set title of locations if exist in breadcrumb
			if(\lib\data::get('breadcrumb', $page_title))
			{
				$page_title = \lib\data::get('breadcrumb', $page_title);
			}
			// replace title of page
			if(!\lib\data::get('page', 'special'))
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
			\lib\data::page($page_title, 'title');
			if(\lib\data::get('page', 'special'))
			{
				\lib\data::global($page_title, 'title');
			}
			else
			{
				\lib\data::global($page_title.' | '.T_(\lib\data::get('site', 'title')), 'title');
			}
		}
		else
		{
			\lib\data::global(T_(\lib\data::get('site', 'title')), 'title');
		}

		\lib\data::global(substr(\lib\data::get('global', 'title'), 0, strrpos(substr(\lib\data::get('global', 'title'), 0, 120), ' ')) . '...', 'short_title');
	}


	private static function set_cms_titles()
	{
		if(!\lib\data::get('datarow'))
		{
			return false;
		}

		// set title
		if(\lib\data::get('datarow', 'title'))
		{
			\lib\data::page(\lib\data::get('datarow', 'title'), 'title');
		}

		// set desc
		if(\lib\data::get('datarow', 'excerpt'))
		{
			\lib\data::page(\lib\data::get('datarow', 'excerpt'), 'desc');
		}
		elseif(\lib\data::get('datarow', 'content'))
		{
			\lib\data::page(\lib\utility\excerpt::extractRelevant(\lib\data::get('datarow', 'content')), 'desc');
		}
		elseif(\lib\data::get('datarow', 'desc'))
		{
			\lib\data::page(\lib\utility\excerpt::extractRelevant(\lib\data::get('datarow', 'desc')), 'desc');
		}

		// set new title
		self::set_title();
	}
}
?>
