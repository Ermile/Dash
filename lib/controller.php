<?php
namespace lib;
use \lib\api;

class controller
{
	use \lib\mvc;
	use \lib\controller\login;
	use \lib\controller\sessions;
	use \lib\controller\ref;


	public static $language = false;
	public $custom_language = false;
	public $api, $model, $view, $method;
	public $model_name, $view_name, $display_name;
	public $debug = true;
	public static $manifest;
	public $datarow = null;


	/**
	 * if display true && method get corridor run view display
	 * @var boolean
	 */
	public $display = true;

	public $route_check_true = false;

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		/**
		 * register shutdown function
		 * after ending code this function is called
		 */
		register_shutdown_function([$this, 'sp_shutdown']);

		$domain = null;
		$param = \lib\url::query();
		if($param)
		{
			$param = '?'.$param;
		}

		$myrep = \lib\url::content();
		switch (\lib\url::module())
		{
			case 'signin':
			case 'login':
				$url = \lib\url::base(). '/enter'. $param;
				$this->redirector($url)->redirect();
				break;

			case 'signup':
				if($myrep !== 'enter')
				{
					$url = \lib\url::base(). '/enter/signup'. $param;
					$this->redirector($url)->redirect();
				}
				break;

			case 'register':

				$url = \lib\url::base(). '/enter/signup'. $param;
				$this->redirector($url)->redirect();
				break;

			case 'signout':
			case 'logout':
				if($myrep !== 'enter')
				{
					$url = \lib\url::base(). '/enter/logout'. $param;
					$this->redirector($url)->redirect();
				}

				break;
		}

		switch (\lib\url::directory())
		{
			case 'account/recovery':
			case 'account/changepass':
			case 'account/verification':
			case 'account/verificationsms':
			case 'account/signin':
			case 'account/login':
				$url = \lib\url::base(). '/enter'. $param;
				$this->redirector($url)->redirect();
				break;

			case 'account/signup':
			case 'account/register':
				$url = \lib\url::base(). '/enter/signup'. $param;
				$this->redirector($url)->redirect();
				break;

			case 'account/logout':
			case 'account/signout':
				$url = \lib\url::base(). '/enter/logout'. $param;
				$this->redirector($url)->redirect();
				break;
		}

		// save referer of users
		$this->save_ref();
		// check if isset remember me and login by this
		$this->check_remeber_login();
		// redirect
		$this->user_country_redirect();
	}



	/**
	 * this function is calling at the end of all codes
	 * @return [type] [description]
	 */
	public function sp_shutdown()
	{
		// close writing sessions and start saving it
		// session_write_close();
		// close the mysql connection
		\lib\db::close();
	}


	/**
	 * [loadModel description]
	 * @return [type] [description]
	 */
	public function loadModel()
	{
		if(!isset($this->loadModel)) $this->loadModel = new \lib\load($this, "model");
		return call_user_func_array(array($this->loadModel, 'method'), func_get_args());
	}


	/**
	 * [loadView description]
	 * @return [type] [description]
	 */
	public function loadView()
	{
		if(!isset($this->loadModel)) $this->loadModel = new \lib\load($this, "view");
		return call_user_func_array(array($this->loadModel, 'method'), func_get_args());
	}


	/**
	 * [_corridor description]
	 * @return [type] [description]
	 */
	public function _corridor()
	{
		if(method_exists($this, 'corridor'))
		{
			$this->corridor();
		}
		if(!$this->method)
		{
			$this->method = 'get';
		}

		$processor_arg = false;

		if(isset($this->model_api_processor))
		{
			$name = $this->model_api_processor->method;
			$args = $this->model_api_processor->args;
			$api_callback = call_user_func_array(array($this->model(), $name), array($args));
			$this->api_callback = $api_callback;

		}

		if(isset($this->caller))
		{
			foreach ($this->caller as $key => $value)
			{
				$args = $value[2];
				if($value[0])
				{
					$caller_callback = call_user_func_array(array($this->model(), "api_".$value[0]), array($args));
					$this->caller[$key][2]->callback = $caller_callback;
				}
			}
		}

		if(\lib\request::json_accept())
		{
			$this->display = false;
		}

		if(!\lib\temp::get('api') && $this->method == 'get' && $this->display)
		{
			$this->view();

			if(isset($this->view_api_processor))
			{
				$name = $this->view_api_processor->method;
				$args = $this->view_api_processor->args;
				if(isset($this->api_callback)) $args->api_callback = $api_callback;
				call_user_func_array(array($this->view(), $name), array($args));

			}

			if(isset($this->caller))
			{
				foreach ($this->caller as $key => $value) {
					$args = $value[2];
					if($value[1])
					{
						$caller_callback = call_user_func_array(array($this->view(), 'caller_'.$value[1]), array($args));
					}
				}
			}

			if($this->display)
			{
				$this->view()->corridor();
			}
		}
		elseif(\lib\temp::get('api') || !$this->display)
		{
			$mycallback = isset($this->api_callback)? $this->api_callback: null;

			if($mycallback !== false && $mycallback !== null)
			{
				debug::result($mycallback);
			}
			$processor_arg = (object) array('force_json'=>true);
		}

		if($this->model)
		{
			$this->model()->_processor($processor_arg);
		}

	}


	/**
	 * [_processor description]
	 * @param  boolean $options [description]
	 * @return [type]           [description]
	 */
	public function _processor($options = false)
	{
		if(is_array($options))
		{
			$options = (object) $options;
		}

		$force_json   = gettype($options) == 'object' && isset($options->force_json)   && $options->force_json   ? true : false;
		$force_stop   = gettype($options) == 'object' && isset($options->force_stop)   && $options->force_stop   ? true : false;
		$not_redirect = gettype($options) == 'object' && isset($options->not_redirect) && $options->not_redirect ? true : false;

		if($not_redirect)
		{
			$this->controller()->redirector = false;
		}


		if(\lib\request::json_accept() || $force_json || \lib\temp::get('api'))
		{
			header('Content-Type: application/json');
			if(isset($this->controller()->redirector) && $this->controller()->redirector)
			{
				debug::msg("redirect", $this->redirector()->redirect(true));
			}
			echo debug::compile(true);
		}
		elseif(!\lib\temp::get('api') && mb_strtolower($_SERVER['REQUEST_METHOD']) == "post")
		{
			$this->redirector();
		}

		if(isset($this->controller()->redirector) && $this->controller()->redirector && !\lib\request::json_accept())
		{
			$this->redirector()->redirect();
		}

		if($force_stop)
		{
			\lib\code::exit();
		}
	}


	/**
	 * [model description]
	 * @return [type] [description]
	 */
	public function model()
	{
		if(!$this->model)
		{
			if($this->model_name)
			{
				$class_name = $this->model_name;
			}
			else
			{
				// $class_name = get_called_class();
				// $class_name = preg_replace("/\\\controller$/", '\model', $class_name);
				$class_name = $this->findParentClass(__FUNCTION__);
			}

			$object = (object) [];
			$object->controller = $this;
			$this->model = new $class_name($object);
		}
		return $this->model;
	}


	/**
	 * [view description]
	 * @return [type] [description]
	 */
	public function view()
	{
		if(!$this->view)
		{
			if($this->view_name)
			{
				$class_name = $this->view_name;
			}
			else
			{
				// $class_name = get_called_class();
				// $class_name = preg_replace("/\\\controller$/", '\\\view', $class_name);
				$class_name = $this->findParentClass(__FUNCTION__);
			}

			$object = (object) [];
			$object->controller = $this;
			$this->view = new $class_name($object);

		}
		return $this->view;
	}


	/**
	 * this function find parent class, if class exist return the name of parent class
	 * else find a parent folder and if class exist use the parent one
	 * @param  [type] $_className the name of class, view or model
	 * @return [type]             return the address of exist class or show error page
	 */
	protected function findParentClass($_className)
	{
		$MyClassName = get_called_class();
		$MyClassName = str_replace("\controller", '\\'.$_className, $MyClassName);

		// if class not exist remove one slash and check it
		if(!class_exists($MyClassName))
		{
			// have more than one back slash for example content\aa\bb\view
			if(substr_count($MyClassName, "\\") > 2)
			{
				$MyClassName = str_replace("\\".$_className, '', $MyClassName);
				$MyClassName = substr($MyClassName, 0, strrpos( $MyClassName, '\\')) . $_className;
			}

			// if after remove one back slash(if exist), class not exist
			if(!class_exists($MyClassName))
			{
				// have more than one back slash for example content\aa\view
				if(substr_count($MyClassName, "\\") == 2)
				{
					$MyClassName = str_replace("\\".$_className, '', $MyClassName);
					$MyClassName = substr($MyClassName, 0, strrpos( $MyClassName, '\\')) . "\home\\" . $_className;
				}
				// have more than one back slash for example content\home
				else
				{
					// i dont know this condtion!
					// do nothing!
				}
			}
			if(!class_exists($MyClassName))
			{
				$MyClassName = preg_replace("/\\\[^\\\]*\\\controller$/", '\home\\'.$_className, get_called_class());
			}
		}

		if(!class_exists($MyClassName))
		{
			\lib\error::page($_className . " not found");
		}

		return $MyClassName;
	}


	/**
	 * route everything ;)
	 */
	public function allow()
	{
		$this->get()->ALL("/.*/");
		$this->post()->ALL("/.*/");
	}


	/**
	 * [check_api description]
	 * @param  [type]  $name           [description]
	 * @param  [type]  $model_function [description]
	 * @param  boolean $view_function  [description]
	 * @return [type]                  [description]
	 */
	public final function check_api($name, $model_function = null, $view_function = false)
	{
		if(!$this->api)
		{
			$this->api = new api($this);
		}
		return $this->api->$name($model_function, $view_function);
	}


	/**
	 * [__call description]
	 * @param  [type] $_name [description]
	 * @param  [type] $_args [description]
	 * @return [type]       [description]
	 */
	public function __call($_name, $_args)
	{
		if(preg_grep("/^$_name$/", array('get', 'post', 'put', 'delete', 'patch', 'link', 'unlink')))
		{
			array_unshift($_args, $_name);
			return call_user_func_array(array($this, 'check_api'), $_args);
		}
		elseif(preg_match("#^inject_((after_|before_)?.+)$#Ui", $_name, $inject))
		{
			return $this->inject($inject[1], $_args);
		}
		elseif(preg_match("#^i(.*)$#Ui", $_name, $icall))
		{
			return $this->mvc_inject_finder($_name, $_args, $icall[1]);
		}

		\lib\error::page(get_called_class()."->$_name()");
	}


	/**
	 * [controller description]
	 * @return [type] [description]
	 */
	public function controller()
	{
		return $this;
	}


	/**
	 * [change_model description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function change_model($name)
	{
		$this->model_name = $name;
	}


	/**
	 * [change_view description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function change_view($name)
	{
		$this->view_name = $name;
	}


	/**
	 * [property description]
	 * @return [type] [description]
	 */
	public function property()
	{
		$args = func_get_args();
		if(count($args) == 1)
		{
			$name = $args[0];
			return $this->$name;
		}
		elseif(count($args) == 2)
		{
			$name = $args[0];
			return $this->$name = $args[1];
		}
	}


	/**
	 * [debug description]
	 * @return [type] [description]
	 */
	public function debug()
	{
		return $this->debug;
	}


	/**
	 * [redirector description]
	 * @return [type] [description]
	 */
	public function redirector()
	{
		if(!isset($this->redirector))	$this->redirector = new \lib\redirector(...func_get_args());
		return $this->redirector;
	}


	public function url($_type = false)
	{
		$new_url = \lib\url::{$_type}();
		return $new_url;
	}
}
?>