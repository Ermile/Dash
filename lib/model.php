<?php
namespace lib;

class model
{
	use mvc;

	/**
	 * construct
	 *
	 * @param      boolean  $object  The object
	 */
	public function __construct($object = false)
	{
		if(!$object) return;
		$this->querys = object();
		$this->controller = $object->controller;
		if(method_exists($this, '_construct'))
		{
			$this->_construct();
		}
	}


	/**
	 * end controller processor
	 *
	 * @param      boolean  $options  The options
	 */
	public function _processor($options = false)
	{
		$this->controller->_processor($options);
	}


	/**
	 * get name
	 *
	 * @param      <type>  $name   The name
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function __get($name)
	{
		if(property_exists($this->controller, $name))
		{
			return $this->controller->$name;
		}
	}


	/**
	 * call corridor
	 *
	 * @param      <type>  $name   The name
	 * @param      <type>  $args   The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function _call_corridor($name, $args)
	{
		preg_match("/^api_(.+)$/", $name, $spilt_name);
		return count($spilt_name) ? $spilt_name : false;
	}


	/**
	 * similar __call
	 *
	 * @param      <type>  $name   The name
	 * @param      <type>  $args   The arguments
	 * @param      <type>  $parm   The parameter
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function _call($name, $args, $parm)
	{
		$method   = $args[0]->method;
		$api_name = "{$method}_$parm[1]";
		$match    = null;
		if(isset($args[0]->match))
		{
			$match = $args[0]->match;
		}
		return $this->$api_name($args[0], $match);
	}
}
?>