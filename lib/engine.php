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
		\lib\engine\prepare::abc();
		\lib\engine\main::start();
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
}
?>
