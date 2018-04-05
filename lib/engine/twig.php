<?php
namespace lib\engine;

class twig
{
	public static function init()
	{
		\lib\data::loadMode('normal');

		if(\lib\request::ajax())
		{
			\lib\data::display_dash("includes/html/display-dash-xhr.html");
			\lib\data::display_enter("includes/html/display-enter-xhr.html");
			\lib\data::display_main("content/main/layout-xhr.html");
			\lib\data::loadMode('ajax');
		}

		$module = str_replace('/', '\\', \lib\engine\mvc::get_dir_address());
		$tmpname = $module.'\\display.html';

		if(strpos($tmpname, '\addons') === 0)
		{
			$tmpname = str_replace('\addons', '', $tmpname);
		}

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
}
?>