<?php
namespace lib\engine;


class main
{
	private static $allow      = [];
	private static $allow_url  = [];
	private static $ctrl_url   = null;

	public static $module_addr = null;
	public static $view_addr   = null;
	public static $controller  = null;


	/**
	 * start main to detect controller and from 0 km to ...
	 * @return [type] [description]
	 */
	public static function start()
	{
		// get best controller
		$finded_controller  = self::find_ctrl();

		if($finded_controller)
		{
			self::load_controller();

			self::load_view();

			if(self::method() !== 'get')
			{
				self::load_model();
			}
		}
	}


	/**
	 * find best controller for this url
	 * @return [type] [description]
	 */
	private static function find_ctrl()
	{
		$my_repo       = '\\'. \lib\engine\content::get();
		$my_module     = '\\'. \lib\url::module();
		$my_child      = '\\'. \lib\url::child();
		$my_controller = null;

		if(\lib\url::child())
		{
			// something like \content_su\tools\test\controller.php
			$my_controller = self::checking($my_repo. $my_module. $my_child);
			if($my_controller)
			{
				self::$ctrl_url = \lib\url::content(). '/'. \lib\url::module(). '/'. \lib\url::child();
				return $my_controller;
			}
		}

		if(\lib\url::module())
		{
			// something like \content_su\tools\home\controller.php
			$my_controller = self::checking($my_repo. $my_module. '\home');
			if($my_controller)
			{
				self::$ctrl_url = \lib\url::content(). '/'. \lib\url::module();
				return $my_controller;
			}

			// something like \content_su\tools\controller.php
			$my_controller = self::checking($my_repo. $my_module);
			if($my_controller)
			{
				self::$ctrl_url = \lib\url::content(). '/'. \lib\url::module();
				return $my_controller;
			}
		}
		if(\lib\engine\content::get())
		{
			// something like \content_su\home\controller.php
			$my_controller = self::checking($my_repo. '\home');
			if($my_controller)
			{
				self::$ctrl_url = \lib\url::content();
				return $my_controller;
			}
		}

		// something like \content\home\controller.php
		$my_controller = self::checking('\content\home');
		if($my_controller)
		{
			self::$ctrl_url = \lib\url::content();
			return $my_controller;
		}

		// nothing found, show error page
		\lib\header::status(404, "nothing found!");
	}


	/**
	 * check controller in project or in dash addons
	 * @param  [type] $_addr [description]
	 * @return [type]        [description]
	 */
	public static function checking($_addr)
	{
		$find            = null;
		$controller_addr = $_addr. '\\controller';

		if(class_exists($controller_addr))
		{
			$find = $controller_addr;
		}
		else
		{
			$controller_addr = '\addons'. $controller_addr;
			if(class_exists($controller_addr))
			{
				$find = $controller_addr;
			}
		}

		if($find)
		{
			self::$module_addr = $_addr;
		}

		return $find;
	}



	/**
	 * load specefic controller
	 * @param  [type] $controller [description]
	 * @return [type]              [description]
	 */
	public static function load_controller()
	{
		$controller = self::$module_addr. '\\controller';

		if(!class_exists($controller))
		{
			\lib\header::status(404, $controller);
		}

		if(is_callable([$controller, 'ready']))
		{
			$controller::ready();
		}

		$url_query = \lib\url::query();
		$url_path  = \lib\url::path();
		$raw_path  = str_replace('?'. $url_query, '', $url_path);

		if($raw_path !== self::$ctrl_url)
		{
			if(!in_array($raw_path, self::$allow_url))
			{
				\lib\header::status(404, "Unavalible");
			}
		}

		if(self::method() !== 'get')
		{
			self::check_allow_method(self::method());
		}
	}


	public static function load_view()
	{
		$view = self::$module_addr. '\\view';

		if(self::method() === 'get' && !\lib\request::json_accept())
		{
			\lib\view::variable();

			if(is_callable([$view, 'config']))
			{
				$view::config();
			}

			\lib\view::twig();
		}
	}


	public static function load_model()
	{
		$model = self::$module_addr. '\\model';

		if(\lib\request::json_accept())
		{
			if(class_exists($model))
			{
				self::check_allow_method(self::method(), true);
			}
		}

		if(self::method() === "post" && !empty(\lib\request::post()))
		{
			\lib\redirect::pwd();
		}
	}


	public static function allow_url($_url)
	{
		array_push(self::$allow_url, $_url);
	}


	public static function allow($_method, $_function_name = null)
	{
		if(!$_function_name)
		{
			$_function_name = $_method;
		}

		self::$allow[$_method] = $_function_name;
	}


	public static function check_allow_method($_method, $_load_model = false)
	{
		if(!array_key_exists($_method, self::$allow))
		{
			\lib\header::status(405);
		}

		if($_load_model)
		{
			$model = self::$module_addr. '\\model';
			$fn    = self::$allow[$_method];

			if(is_callable([$model, $fn]))
			{
				$model::$fn();
			}
			else
			{
				\lib\header::status(500, "Function $fn not exist!");
			}
		}
	}


	public static function method($_name = null)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		$method = mb_strtolower($method);
		if($_name)
		{
			if($_name === $method)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $method;
		}
	}
}
?>