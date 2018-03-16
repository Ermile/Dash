<?php
namespace lib\engine;
class main
{
	private static $ctrl_name  = null;
	public static $controller = null;


	/**
	 * start main to detect controller and from 0 km to ...
	 * @return [type] [description]
	 */
	public static function start()
	{
		// get best controller
		self::$ctrl_name  = self::find_ctrl();
		// try to load it
		self::loadController(self::$ctrl_name);
	}


	/**
	 * find best controller for this url
	 * @return [type] [description]
	 */
	private static function find_ctrl()
	{
		$myRepo   = '\\'. \lib\content::get();
		$myModule = '\\'. \lib\url::module();
		$myChild  = '\\'. \lib\url::child();
		$myCtrl   = null;

		if(\lib\url::child())
		{
			// something like \content_su\tools\test\controller.php
			$myCtrl = self::checking($myRepo. $myModule. $myChild);
			if($myCtrl)
			{
				return $myCtrl;
			}
		}

		if(\lib\url::module())
		{
			// something like \content_su\tools\home\controller.php
			$myCtrl = self::checking($myRepo. $myModule. '\home');
			if($myCtrl)
			{
				return $myCtrl;
			}

			// something like \content_su\tools\controller.php
			$myCtrl = self::checking($myRepo. $myModule);
			if($myCtrl)
			{
				return $myCtrl;
			}
		}

		// something like \content_su\home\controller.php
		$myCtrl = self::checking($myRepo. '\home');
		if($myCtrl)
		{
			return $myCtrl;
		}

		// something like \content\home\controller.php
		$myCtrl = self::checking('\content\home');
		if($myCtrl)
		{
			return $myCtrl;
		}

		// nothing found, show error page
		\lib\error::page("nothing found!");
	}


	/**
	 * check controller in project or in dash addons
	 * @param  [type] $_addr [description]
	 * @return [type]        [description]
	 */
	public static function checking($_addr)
	{
		$find     = null;
		$ctrlAddr = $_addr. '\\controller';

		if(class_exists($ctrlAddr))
		{
			$find = $ctrlAddr;
		}
		else
		{
			$ctrlAddr = '\addons'. $ctrlAddr;
			if(class_exists($ctrlAddr))
			{
				$find = $ctrlAddr;
			}
		}

		return $find;
	}


	/**
	 * return name of current controller
	 * @param  [type] $_obj [description]
	 * @return [type]       [description]
	 */
	public static function controller_get($_obj = null)
	{
		if($_obj)
		{
			return self::$controller;
		}

		return self::$ctrl_name;
	}


	/**
	 * set name of new controller
	 * @param  [type] $_addr [description]
	 * @return [type]        [description]
	 */
	public static function controller_set($_addr)
	{
		self::$controller = $_addr;
	}


	/**
	 * load specefic controller
	 * @param  [type] $_controller [description]
	 * @return [type]              [description]
	 */
	public static function loadController($_controller)
	{
		if(!class_exists($_controller))
		{
			\lib\error::page($_controller);
		}
		$my_controller    = new $_controller;
		self::$controller = $my_controller;

		// some special function that call on each module
		// if needed for project
		if(method_exists($my_controller, 'project'))
		{
			$my_controller->project();
		}
		// call on repository, for example check permission of content and etc.
		if(method_exists($my_controller, 'repository'))
		{
			$my_controller->repository();
		}
		// for special module, call ready func
		if(method_exists($my_controller, 'ready'))
		{
			$my_controller->ready();
		}

		// recheck, maybe change in above function in project
		if(self::controller_get() !== $_controller)
		{
			self::loadController(self::controller_get());
			return;
		}

		$my_controller->_corridor();
	}
}
?>