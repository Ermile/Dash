<?php
namespace lib;
class main
{
	public static $controller   = null;
	public static $url_property = null;
	public static $prv_class    = null;
	public static $myrep        = null;
	public static $prv_method   = null;
	public static $tracks       = [];


	/**
	 * check controller is exits or no
	 *
	 * @param      <type>  $_controller_name  The controller name
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	public function check_controller($_controller_name)
	{
		$find = null;

		if(!class_exists($_controller_name))
		{
			$find = null;
		}
		else
		{
			$find = $_controller_name;
		}

		if(!$find)
		{
			$controller_name = '\addons'. $_controller_name;
			if(!class_exists($controller_name))
			{
				$find = null;
			}
			else
			{
				$find = $controller_name;
			}
		}
		return $find;
	}


	/**
	 * Adds a track.
	 *
	 * @param      <type>  $_name      The name
	 * @param      <type>  $_function  The function
	 */
	public function add_track($_name, $_function)
	{
		array_push(self::$tracks, array($_name, $_function));
	}


	private static function getModule()
	{
		// get module
		$myModule = \lib\url::module();
		if($myModule === null)
		{
			$myModule = 'home';
		}
		return $myModule;
	}


	private static function getChild()
	{
		// get child
		$myChild = \lib\url::child();
		if($myChild === null)
		{
			$myChild = 'home';
		}
		return $myChild;
	}


	/**
	 * Adds controller tracks.
	 */
	public function add_controller_tracks()
	{



		if(\lib\url::dir(2))
		{
			$this->add_track('api_childs', function()
			{
				$controller_name  = '\\'. self::$myrep;
				$controller_name .= '\\'. self::getModule();
				$controller_name .= '\\'. self::getChild();
				$controller_name .= '\\'. \lib\url::dir(2);
				$controller_name .= '\\controller';
				return $this->check_controller($controller_name);
			});
		}

		$this->add_track('default', function()
		{
			return router::get_controller();
		});

		$this->add_track('class_method', function()
		{
			$controller_name	= '\\'.self::$myrep.'\\'.self::getModule().'\\'.self::getChild().'\\controller';
			self::$prv_class	= self::getModule();
			return $this->check_controller($controller_name);
		});


		$this->add_track('class_home', function()
		{
			if((!isset(self::$url_property[1]) || self::$url_property[1] != self::getChild()) && self::getChild() != 'home')
			{
				router::add_url_property(self::getChild());
			}
			self::$prv_method = self::getChild();
			router::set_method('home');
			$controller_name = '\\'.self::$myrep.'\\'.self::getModule().'\\'.self::getChild().'\\controller';

			return $this->check_controller($controller_name);
		});

		$this->add_track('class', function(){
			router::set_class(self::$prv_class);
			$controller_name = '\\'.self::$myrep.'\\'.self::getModule().'\\controller';

			return $this->check_controller($controller_name);
		});

		$this->add_track('home_home', function(){
			if((!isset(self::$url_property[0]) || self::$url_property[0] != self::getModule()) && self::getModule() != 'home')
			{
				router::add_url_property(self::getModule());
			}
			router::set_class('home');
			$controller_name = '\\'.self::$myrep.'\\'.self::getModule().'\\'.self::getChild().'\\controller';

			return $this->check_controller($controller_name);
		});

		$this->add_track('home', function(){
			router::set_class('home');
			$controller_name = '\\'.self::$myrep.'\\'.self::getModule().'\\controller';

			return $this->check_controller($controller_name);
		});
	}

	public function controller_finder(){
		self::$url_property = router::get_url_property(-1);
		self::$myrep        = \lib\content::name();

		$this->add_controller_tracks();

		foreach (self::$tracks as $key => $value)
		{
			$track = self::$tracks[$key][1];
			$controller_name = $track();
			if($controller_name) break;
		}
		$this->loadController($controller_name);
	}

	public function __construct()
	{

		$this->controller_finder();

	}

	public function loadController($controller_name)
	{

		router::set_controller($controller_name);
		if(!class_exists($controller_name))
		{
			error::page($controller_name);
		}


		$controller = new $controller_name;
		self::$controller = $controller;


		// some special function that call on each module
		// if needed for project
		if(method_exists($controller, 'project'))
		{
			$controller->project();
		}
		// call on repository, for example check permission of content and etc.
		if(method_exists($controller, 'repository'))
		{
			$controller->repository();
		}
		// for special module, call ready func
		if(method_exists($controller, 'ready'))
		{
			$controller->ready();
		}


		if(router::get_controller() !== $controller_name)
		{
			$this->controller_finder();
			return;
		}


		if(count(router::get_url_property(-1)) > 0 && $controller->route_check_true === false)
		{
			if(\lib\content::name() === 'content')
			{
				\lib\app\template::$module = \lib\url::module();

				if(\lib\app\template::find())
				{
					$controller->display_name     = \lib\app\template::$display_name;
					$controller->route_check_true = \lib\app\template::$route_check_true;
				}
				else
				{
					error::page('Unavailable');
				}
			}
			else
			{
				error::page('Unavailable');
			}

		}
		$controller->_corridor();
	}
}
?>