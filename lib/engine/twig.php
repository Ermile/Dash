<?php
namespace dash\engine;

class twig
{
	public static function init()
	{
		\dash\data::loadMode('normal');

		if(\dash\request::ajax())
		{
			// set file name of static parts
			\dash\data::display_dash("includes/html/display-dash-xhr.html");
			\dash\data::display_enter("includes/html/display-enter-xhr.html");
			// generate main filename automatically because used on various condition
			$file_xhr_main = substr(\dash\data::display_main(), 0, -5). '-xhr.html';
			\dash\data::display_main($file_xhr_main);
		}

		$module = str_replace('/', '\\', \dash\engine\mvc::get_dir_address());
		$tmpname = $module.'\\display.html';
		// show error if display is not exist
		$tmpname_addr = \autoload::fix_os_path(root. ltrim($tmpname, '\\'));
		if(!is_file($tmpname_addr))
		{
			// display file is not exist in root
			$tmpname_addr = \autoload::fix_os_path(addons. ltrim($tmpname, '\\'));
			if(!is_file($tmpname_addr))
			{
				if(\dash\url::content() === null && \dash\app\template::$finded_template)
				{
					$tmpname_addr = root. \dash\app\template::$display_name;
					$tmpname_addr = \autoload::fix_os_path($tmpname_addr);
					if(!is_file($tmpname_addr))
					{
						\dash\header::status(206, "without display");
						return false;
					}
					$tmpname = \dash\app\template::$display_name;
				}
				else
				{
					\dash\header::status(206, "without display");
					return false;
				}
			}
		}
		if(strpos($tmpname, '\addons') === 0)
		{
			$tmpname = str_replace('\addons', '', $tmpname);
		}

		\dash\data::pagination(\dash\utility\pagination::page_number());
		\dash\data::paginationDetail(\dash\utility\pagination::detail());

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

		\dash\engine\twigAddons::init($twig);

		$twig->addGlobal("session", $_SESSION);

		if(\dash\engine\dev::debug())
		{
			$twig->addExtension(new \Twig_Extension_Debug());
		}

		$twig->addExtension(new \Twig_Extensions_Extension_I18n());

		$template = $twig->loadTemplate($tmpname);
		if(\dash\request::ajax())
		{
			\dash\data::global_debug(\dash\notif::get());
			$xhr_render = $template->render(\dash\data::get());

			echo json_encode(\dash\data::get('global'));
			echo "\n";
			echo $xhr_render;
		}
		else
		{
			$template->display(\dash\data::get());
		}


	}
}
?>