<?php
namespace lib;


class controller
{
	public $display_name = null;

	public $model_name   = null;
	public $view_name    = null;

	public $debug    = null;

	private $allow       = [];

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		// @check
		// check if isset remember me and login by this
		\lib\user::check_remeber_login();

		// redirect
		\lib\user::user_country_redirect();
	}


	public function allow($_method, $_function_name = null)
	{
		if(!$_function_name)
		{
			$_function_name = $_method;
		}
		$this->allow[$_method] = $_function_name;
	}


	private function method_function($_method)
	{
		if(!array_key_exists($_method, $this->allow))
		{
			\lib\header::status(405);
		}

		if(is_callable([$this->model, $this->allow[$_method]]))
		{
			return call_user_func_array([$this->model, $this->allow[$_method]], []);
		}
		else
		{
			\lib\header::status(500, "Function ". $this->allow[$_method]. " not exist!");
		}
	}


	/**
	* RUN VEIW AND MODE FUNCTIONS
	*/
	public function _corridor()
	{
		$display = true;
		if(\lib\request::json_accept())
		{
			$display = false;
		}

		if($this->method() === 'get' && $display)
		{
			$this->view();

			if($display)
			{
				$this->view()->corridor();
			}
		}
		elseif(!$display)
		{
			$this->model();
			if($this->model)
			{
				$this_method = $this->method();
				$this->method_function($this_method);
			}
		}
		else
		{
			\lib\header::status(424);
		}

		if($this->method() === "post")
		{
			\lib\redirect::pwd();
		}
	}


	public function method($_name = null)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		$method = mb_strtolower($method);
		if($_name)
		{
			if($_name === $method)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $method;
		}
	}


	/**
	 * [model description]
	 * @return [type] [description]
	 */
	public function model()
	{

		if($this->model_name)
		{
			$class_name = $this->model_name;
		}
		else
		{
			$class_name = get_called_class();
			$class_name = preg_replace("/\\\controller$/", '\model', $class_name);
		}

		if(class_exists($class_name))
		{
			$this->model = $class_name;
			return $this->model;
		}

		return null;
	}


	/**
	 * [view description]
	 * @return [type] [description]
	 */
	public function view()
	{
		if($this->view_name)
		{
			$class_name = $this->view_name;
		}
		else
		{
			$class_name = get_called_class();
			$class_name = preg_replace("/\\\controller$/", '\\\view', $class_name);
		}

		if(class_exists($class_name))
		{
			$object             = (object) [];
			$object->controller = $this;
			$this->view         = new $class_name($object);
			return $this->view;
		}
		return null;
	}

	public function controller()
	{
		return $this;
	}

	public function debug()
	{
		return $this->debug;
	}
}
?>