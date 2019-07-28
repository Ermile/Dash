<?php
namespace dash\engine;


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
		$my_repo       = '\\'. \dash\engine\content::get();
		$my_module     = '\\'. \dash\url::module();
		$my_child      = '\\'. \dash\url::child();
		$my_controller = null;

		if(\dash\url::child() !== null)
		{
			// something like \content_su\tools\test\controller.php
			$my_controller = self::checking($my_repo. $my_module. $my_child);
			if($my_controller)
			{
				self::$routed_addr = \dash\url::content(). '/'. \dash\url::module(). '/'. \dash\url::child();
				return $my_controller;
			}
		}

		if(\dash\url::module() !== null)
		{
			// something like \content_su\tools\home\controller.php
			$my_controller = self::checking($my_repo. $my_module. '\home');
			if($my_controller)
			{
				self::$routed_addr = \dash\url::content(). '/'. \dash\url::module();
				return $my_controller;
			}

			// something like \content_su\tools\controller.php
			$my_controller = self::checking($my_repo. $my_module);
			if($my_controller)
			{
				self::$routed_addr = \dash\url::content(). '/'. \dash\url::module();
				return $my_controller;
			}
		}

		if(\dash\engine\content::get())
		{
			// something like \content_su\home\controller.php
			$my_controller = self::checking($my_repo. '\home');
			if($my_controller)
			{
				self::$routed_addr = \dash\url::content();
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
			self::$routed_addr = \dash\url::pwd();
			return $template;
		}

		// nothing found, show error page
		\dash\header::status(501, T_("Hey, Read documentation and start your project!"));
	}



	private static function find_tmplate()
	{
		if(\dash\url::content())
		{
			return false;
		}

		$template = \dash\app\template::find();

		if(\dash\app\template::$finded_template)
		{
			self::$folder_addr = \dash\app\template::$display_name;
			return true;
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
				$_addr = '\addons'. $_addr;
			}
			else
			{
				$_addr = trim($_addr, '\\');
				$temp_addr = \autoload::fix_os_path($_addr);
				if(is_dir(root. $temp_addr))
				{
					self::$only_folder = true;
					$find              = true;
				}
				elseif(is_dir(addons. $temp_addr))
				{
					// load addons only folder
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
			\dash\header::status(409, $my_controller);
		}

		// run content default function for set something if needed
		$content_controller = \dash\engine\content::get().'\\controller';
		if(is_callable([$content_controller, 'routing']))
		{
			$content_controller::routing();
		}

		if(is_callable([$my_controller, 'routing']))
		{
			$my_controller::routing();
		}

		// generate real address of current page
		$real_address = trim(\dash\url::content(). '/'. \dash\url::directory(), '/');
		if($real_address === '')
		{
			$real_address = null;
		}
		// if we are in another address of current routed in controller, double check
		if(trim(self::$routed_addr, '/') != $real_address)
		{
			// if this url has no custom licence, block it
			if(!\dash\open::license())
			{
				$template = self::find_tmplate();
				if($template)
				{
					self::$routed_addr = \dash\url::pwd();
					return $template;
				}
				else
				{
					\dash\header::status(404, T_("We can't find the page you're looking for!"));
				}
			}
		}
	}


	private static function load_view()
	{
		$my_view = self::$folder_addr. '\\view';
		if(\dash\request::is('get') && !\dash\request::json_accept())
		{
			\dash\engine\view::variable();

			// run content default function for set something if needed
			$content_view = \dash\engine\content::get().'\\view';
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
			$my_view_function = \dash\open::license(null, true);
			if($my_view_function && is_callable([$my_view, $my_view_function]))
			{
				$my_view::$my_view_function();
			}

			// combine two type of set title into one
			if(\dash\url::content() === null)
			{
				\dash\data::datarow(\dash\app\template::$datarow);
				\dash\engine\view::set_cms_titles();
			}
			else
			{
				\dash\engine\view::set_title();
			}

			\dash\engine\view::lastChanges();
			\dash\engine\twig::init();
		}
	}


	/**
	 * try to load model if needed and empty page post parameter
	 * @return [type] [description]
	 */
	private static function load_model()
	{
		$my_model = self::$folder_addr. '\\model';
		if(!\dash\request::is('get'))
		{
			if(class_exists($my_model))
			{
				$my_model_function = \dash\open::license(null, true);
				if($my_model_function && is_callable([$my_model, $my_model_function]))
				{
					$my_model::$my_model_function();
				}
				else
				{
					// show not implemented message
					\dash\header::status(501);
				}
			}
			else
			{
				// model does not exist in this folder, show not acceptable message
				\dash\header::status(406);
			}

			\dash\code::compile();
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