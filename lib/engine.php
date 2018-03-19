<?php
namespace lib;
/**
 * dash main configure
 */
class engine
{

	public function __construct()
	{
		// check debug and set php ini
		\lib\engine\dev::set_php_ini();

		// block baby to not allow to harm yourself :/
		\lib\engine\baby::block();

		// check min requirement to run dash core!
		\lib\engine\init::minimum_requirement();

		// detect url and start work with them as first lib used by another one
		\lib\url::initialize();

		// detect language and if need set the new language
		\lib\language::detect_language();

		// check comming soon page
		\lib\engine\init::coming_soon();

		// check need redirect for lang or www or https or main domain
		\lib\engine\init::appropriate_url();

		// start session
		\lib\session::start();

		// LUNCH !
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
