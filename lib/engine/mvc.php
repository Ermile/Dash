<?php
namespace lib\engine;


class mvc
{
	private static $controller_addr = null;
	private static $folder_addr     = null;
	private static $allow           = [];
	private static $allow_url       = [];


	/**
	 * start main to detect controller and from 0 km to ...
	 * @return [type] [description]
	 */
	public static function fire()
	{
		// get best controller
		$finded_controller  = self::find_ctrl();

		if($finded_controller)
		{
			self::load_controller();

			self::load_view();

			self::load_model();
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
				self::$controller_addr = \lib\url::content(). '/'. \lib\url::module(). '/'. \lib\url::child();
				return $my_controller;
			}
		}

		if(\lib\url::module())
		{
			// something like \content_su\tools\home\controller.php
			$my_controller = self::checking($my_repo. $my_module. '\home');
			if($my_controller)
			{
				self::$controller_addr = \lib\url::content(). '/'. \lib\url::module();
				return $my_controller;
			}

			// something like \content_su\tools\controller.php
			$my_controller = self::checking($my_repo. $my_module);
			if($my_controller)
			{
				self::$controller_addr = \lib\url::content(). '/'. \lib\url::module();
				return $my_controller;
			}
		}

		if(\lib\engine\content::get())
		{
			// something like \content_su\home\controller.php
			$my_controller = self::checking($my_repo. '\home');
			if($my_controller)
			{
				self::$controller_addr = \lib\url::content();
				return $my_controller;
			}
		}

		// something like \content\home\controller.php
		$my_controller = self::checking('\content\home');
		if($my_controller)
		{
			self::$controller_addr = '/';
			return $my_controller;
		}

		// nothing found, show error page
		\lib\header::status(501, "Hey, Read documentation and start your project!");
	}


	/**
	 * check controller in project or in dash addons
	 * @param  [type] $_addr [description]
	 * @return [type]        [description]
	 */
	private static function checking($_addr)
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
			// set module addr to use in all other function for addressing
			self::$folder_addr = $_addr;
		}

		return $find;
	}



	/**
	 * load specefic controller
	 * @param  [type] $controller [description]
	 * @return [type]              [description]
	 */
	private static function load_controller()
	{
		$controller = self::$folder_addr. '\\controller';
		if(!class_exists($controller))
		{
			\lib\header::status(409, $controller);
		}

		if(is_callable([$controller, 'run']))
		{
			$controller::run();
		}

		$real_address = trim(\lib\url::content(). '/'. \lib\url::directory(), '/');
		if(!$real_address)
		{
			$real_address = null;
		}

		// if we are in another address of current routed in controller, double check
		if(self::$controller_addr != $real_address)
		{
			if(!in_array(\lib\url::directory(), self::$allow_url))
			{
				\lib\header::status(404, "We can't find the page you're looking for!");
			}
		}
	}


	private static function load_view()
	{
		$view = self::$folder_addr. '\\view';

		if(\lib\request::is('get') && !\lib\request::json_accept())
		{
			\lib\view::variable();

			if(is_callable([$view, 'run']))
			{
				$view::run();
			}

			if(array_key_exists('get', self::$allow))
			{
				$view_function = self::$allow['get'];

				if(is_callable([$view, $view_function]))
				{
					$view::$view_function();
				}
			}

			$display_addr = root. ltrim(self::$folder_addr, '\\');
			$display_addr = str_replace('\\', DIRECTORY_SEPARATOR, $display_addr);
			$display_addr = str_replace('/', DIRECTORY_SEPARATOR, $display_addr);
			if(file_exists($display_addr))
			{
				\lib\view::twig();
			}
		}
	}


	private static function load_model()
	{
		$model = self::$folder_addr. '\\model';

		$method = \lib\request::is();

		if($method !== 'get' || \lib\request::json_accept())
		{
			if(class_exists($model))
			{
				if(array_key_exists($method, self::$allow))
				{
					$model_function = self::$allow[$method];
				}
				else
				{
					$model_function = $method;
				}

				if(is_callable([$model, $model_function]))
				{
					$model::$model_function();
				}
				else
				{
					\lib\header::status(405);
				}
			}
			else
			{
				// model does not exist in this folder
				\lib\header::status(501);
			}
		}

		if(\lib\request::is('post') && !empty(\lib\request::post()))
		{
			\lib\redirect::pwd();
		}
	}


	public static function get_dir_address()
	{
		return self::$folder_addr;
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
}
?>