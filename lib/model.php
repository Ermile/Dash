<?php
namespace lib;

class model
{
	use mvc;
	// query lists
	public $querys;

	// sql object
	public $sql;

	public $commit = array();
	public $rollback = array();

	public $transaction = true;
	public function __construct($object = false){
		if(!$object) return;
		$this->querys = object();
		$this->controller = $object->controller;
		if(method_exists($this, '_construct')){
			$this->_construct();
		}
	}

	public function _processor($options = false)
	{
		$this->controller->_processor($options);
	}


	public function __get($name){
		if(property_exists($this->controller, $name)){
			return $this->controller->$name;
		}
	}

	public function _call_corridor($name, $args){
		preg_match("/^api_(.+)$/", $name, $spilt_name);
		return count($spilt_name) ? $spilt_name : false;
	}

	public function _call($name, $args, $parm){
		$method = $args[0]->method;
		$api_name = "{$method}_$parm[1]";
		$match = null;
		if(isset($args[0]->match)){
			$match = $args[0]->match;
		}
		return $this->$api_name($args[0], $match);
	}
}
?>