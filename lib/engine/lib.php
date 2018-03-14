<?php
namespace lib\engine;
/**
 * autoload engine core lib
 */
class lib
{
	public $prefix;
	public $static = false;
	public function __construct($_args = null, $_static = false)
	{
		$this->static = $_static;
		$this->prefix = $_args ? "\\". trim($_args[0], "\\"). "\\" : "\\";
	}

	/**
	 * { function_description }
	 *
	 * @param      <type>  $name   The name
	 * @param      <type>  $args   The arguments
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	public function __call($name, $args)
	{
		$class_name = "lib{$this->prefix}{$name}";
		if(class_exists($class_name))
		{
			if($this->static === true)
			{
				return $class_name;
			}
			return new $class_name(...$args);
		}

		\lib\error::core("lib\\{$name}");
	}
}