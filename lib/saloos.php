<?php
namespace lib;
/**
 * saloos main configure
 */
class saloos
{
	// @var saloos core current version
	const version = '9.6.8';

	// @var saloos core current commit number
	// now get it automatically from git commands
	// const iversion = 726;

	// @var current version last update date
	// now get it automatically from git last commit date

	public static $protocol_type = 'http';

	/**
	 * constractor
	 */
	public function __construct()
	{
		$creator = stream_resolve_include_path('creator.php');
		if($creator)
		{
			require_once $creator;
		}
		else
		{
			self::lib()->router();
			self::lib()->define();

			self::lib()->main();
		}
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
			$class = '\lib\saloos\is';
			return call_user_func_array(array($class, $aName[1]), $args);
		}
		elseif($name == 'lib_static')
		{
			$class = '\\lib\\saloos\lib';
			return new $class($args, true);
		}

		$class = '\\lib\\saloos\\'.$name;
		return new $class($args);
	}


	/**
	 * @return saloos commit count from Git
	 */
	public static function getCommitCount($_saloos = true)
	{
		$commitCount = null;
		try
		{
			if($_saloos)
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
	 * @return last version of Saloos
	 */
	public static function getLastVersion()
	{
		if(self::command_exists('git'))
		{
			$commitCount = exec('git rev-list --all --count');
		}
		return self::version;
	}


	/**
	 * @return last Update of Saloos
	 */
	public static function getLastUpdate($_saloos = true)
	{
		$commitDate = null;
		try
		{
			if($_saloos)
			{
				chdir(core);
			}
			if(self::command_exists('git'))
			{
				$commitDate = new \DateTime(trim(exec('git log -n1 --pretty=%ci HEAD')));
				$commitDate = $commitDate->format('Y-m-d');
			}
		}
		catch (Exception $e)
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