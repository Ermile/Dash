<?php
namespace lib\engine;


class mvc
{
	private static $folder_addr = null;
	private static $routed_addr = null;
	private static $only_folder = null;


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
				self::$routed_addr = \lib\url::content(). '/'. \lib\url::module(). '/'. \lib\url::child();
				return $my_controller;
			}
		}

		if(\lib\url::module())
		{
			// something like \content_su\tools\home\controller.php
			$my_controller = self::checking($my_repo. $my_module. '\home');
			if($my_controller)
			{
				self::$routed_addr = \lib\url::content(). '/'. \lib\url::module();
				return $my_controller;
			}

			// something like \content_su\tools\controller.php
			$my_controller = self::checking($my_repo. $my_module);
			if($my_controller)
			{
				self::$routed_addr = \lib\url::content(). '/'. \lib\url::module();
				return $my_controller;
			}
		}

		if(\lib\engine\content::get())
		{
			// something like \content_su\home\controller.php
			$my_controller = self::checking($my_repo. '\home');
			if($my_controller)
			{
				self::$routed_addr = \lib\url::content();
				return $my_controller;
			}
		}

		// something like \content\home\controller.php
		$my_controller = self::checking('\content\home');
		if($my_controller)
		{
			self::$routed_addr = '/';
			return $my_controller;
		}

		$template = self::find_tmplate();
		if($template)
		{
			self::$routed_addr = \lib\url::pwd();
			return $template;
		}

		// nothing found, show error page
		\lib\header::status(501, "Hey, Read documentation and start your project!");
	}



	private static function find_tmplate()
	{
		if(\lib\url::content())
		{
			return false;
		}

		$template = \lib\app\template::find();

		if(\lib\app\template::$finded_template)
		{
			self::$folder_addr = \lib\app\template::$display_name;

		}
	}


	/**
	 * check controller in project or in dash addons
	 * @param  [type] $_addr [description]
	 * @return [type]        [description]
	 */
	private static function checking($_addr)
	{
		$find   = null;
		$myctrl = $_addr. '\\controller';

		if(class_exists($myctrl))
		{
			$find = $myctrl;
		}
		else
		{
			$myctrl = '\addons'. $myctrl;
			if(class_exists($myctrl))
			{
				$find = $myctrl;
			}
			else
			{
				$_addr = trim($_addr, '\\');
				$_addr = str_replace('\\', '/', $_addr);
				if(is_dir(root. $_addr))
				{
					self::$only_folder = true;
					$find              = true;
				}
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
		$my_controller = self::$folder_addr. '\\controller';
		if(!class_exists($my_controller) && !self::$only_folder)
		{
			\lib\header::status(409, $my_controller);
		}

		// run content default function for set something if needed
		$content_controller = \lib\engine\content::get().'\\controller';
		if(is_callable([$content_controller, 'routing']))
		{
			$content_controller::routing();
		}

		if(is_callable([$my_controller, 'routing']))
		{
			$my_controller::routing();
		}

		// generate real address of current page
		$real_address = trim(\lib\url::content(). '/'. \lib\url::directory(), '/');
		if(!$real_address)
		{
			$real_address = null;
		}
		// if we are in another address of current routed in controller, double check
		if(trim(self::$routed_addr, '/') != $real_address)
		{
			// if this url has no custom licence, block it
			if(!\lib\open::license())
			{
				$template = self::find_tmplate();
				if($template)
				{
					self::$routed_addr = \lib\url::pwd();
					return $template;
				}
				else
				{
					\lib\header::status(404, "We can't find the page you're looking for!");
				}
			}
		}
	}


	private static function load_view()
	{
		$my_view = self::$folder_addr. '\\view';
		if(\lib\request::is('get') && !\lib\request::json_accept())
		{
			\lib\engine\view::variable();

			// run content default function for set something if needed
			$content_view = \lib\engine\content::get().'\\view';
			if(is_callable([$content_view, 'config']))
			{
				$content_view::config();
			}

			// run default function of view
			if(is_callable([$my_view, 'config']))
			{
				$my_view::config();
			}

			// call custom function if exist
			$my_view_function = \lib\open::license(null, true);
			if($my_view_function && is_callable([$my_view, $my_view_function]))
			{
				$my_view::$my_view_function();
			}

			$display_addr = root. ltrim(self::$folder_addr, '\\');
			$display_addr = str_replace('\\', DIRECTORY_SEPARATOR, $display_addr);
			$display_addr = str_replace('/', DIRECTORY_SEPARATOR, $display_addr);
			if(file_exists($display_addr))
			{
				\lib\engine\twig::init();
			}
		}
	}


	/**
	 * try to load model if needed and empty page post parameter
	 * @return [type] [description]
	 */
	private static function load_model()
	{
		$my_model = self::$folder_addr. '\\model';
		if(!\lib\request::is('get') || \lib\request::json_accept())
		{
			if(class_exists($my_model))
			{
				$my_model_function = \lib\open::license(null, true);
				if($my_model_function && is_callable([$my_model, $my_model_function]))
				{
					$my_model::$my_model_function();
				}
				else
				{
					// show not implemented message
					\lib\header::status(501);
				}
			}
			else
			{
				// model does not exist in this folder, show not acceptable message
				\lib\header::status(406);
			}
		}

		if(\lib\request::is('post') && !empty(\lib\request::post()))
		{
			\lib\redirect::pwd();
		}
	}

	/**
	 * show address of current module dir
	 * @return [type] [description]
	 */
	public static function get_dir_address()
	{
		return self::$folder_addr;
	}
}
?>