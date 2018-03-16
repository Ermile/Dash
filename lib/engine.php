<?php
namespace lib;
/**
 * dash main configure
 */
class engine
{

	public function __construct()
	{
		\lib\engine\init::run();

		self::lib()->router();

		\lib\engine\prepare::abc();
		\lib\engine\main::start();
	}


	public static function route()
	{
		$route = new router\route(false);
		call_user_func_array(array($route, 'check_route'), func_get_args());

		return $route;
	}


	public static function __callstatic($name, $args)
	{
		if(preg_match("/^is_(.*)$/", $name, $aName))
		{
			$class = '\lib\engine\is';
			return call_user_func_array(array($class, $aName[1]), $args);
		}
		elseif($name == 'lib_static')
		{
			$class = '\\lib\\engine\lib';
			return new $class($args, true);
		}

		$class = '\\lib\\engine\\'.$name;
		return new $class($args);
	}


	/**
	 * @return dash commit count from Git
	 */
	public static function getCommitCount($_dash = true)
	{
		$commitCount = null;
		try
		{
			if($_dash)
			{
				chdir(core);
			}
			if(self::command_exists('git'))
			{
				$commitCount = exec('git rev-list --all --count');
			}
		}
		catch (Exception $e)
		{
			$commitCount = 0;
		}

		return $commitCount;
	}



	/**
	 * @return last version of dash
	 */
	public static function getLastVersion()
	{
		// if(self::command_exists('git'))
		// {
		// 	$commitCount = exec('git rev-list --all --count');
		// }
		return \lib\engine\version::get();
	}


	/**
	 * @return last Update of dash
	 */
	public static function getLastUpdate($_dash = true)
	{
		$commitDate = null;
		try
		{
			if($_dash)
			{
				chdir(core);
			}
			if(self::command_exists('git'))
			{
				$commitDate = new \DateTime(trim(exec('git log -n1 --pretty=%ci HEAD')));
				$commitDate = $commitDate->format('Y-m-d');
			}
		}
		catch (\Exception $e)
		{
			$commitDate = date();
		}

		return $commitDate;
	}

	public static function command_exists($_command)
	{
		// on windows use where other use which
		$whereIsCommand = (PHP_OS == 'WINNT') ? 'where' : 'which';
		// execute command
		$returnVal      = shell_exec("$whereIsCommand $_command");
		// return command exist or not
		return (empty($returnVal) ? false : true);
	}
}
?>
