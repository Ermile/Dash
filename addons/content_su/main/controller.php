<?php
namespace content_su\main;

class controller extends \mvc\controller
{
	/**
	 * check permission
	 */
	public function ready()
	{
		// do for exception url
		self::install_first_time();
		// check permission
		self::_permission();

		$this->suFindDisplay();
	}


	/**
	 * check if not installed database
	 * install databse
	 */
	protected function install_first_time()
	{
		// run if get is set and no database exist
		if($this->suModule('raw') == 'install' && \dash\request::get('time') == 'first_time')
		{
			if(!\dash\db::count_table())
			{
				require_once(lib."engine/install.php");
				// this code exit the code
				\dash\engine\mvc::$controller->_processor(['force_stop' => true, 'force_json' => false]);
			}
			else
			{
				\dash\header::status(404, T_("System was installed!"));
			}
		}
	}


	/**
	 * check permission to load su
	 *
	 * @param      <type>   $_content  The content
	 * @param      <type>   $_module   The module
	 * @param      <type>   $_perm     The permission
	 * @param      boolean  $_login    The login
	 */
	public function _permission()
	{
		// if user is not login then redirect
		if(!\dash\user::login())
		{
			\dash\redirect::to(\dash\url::base(). '/enter');
			return ;
		}

		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		if(\dash\url::isLocal() && false)
		{
			// on tld dev open the su to upgrade for test
		}
		else
		{
			if(\dash\permission::access_su())
			{
				// the user have permission of su
			}
			else
			{
				// set 404 to the user never underestand this url is exist ;)
				\dash\header::status(404);
			}
		}
	}




	/**
	 * find best display for this page!
	 * @return [type] [description]
	 */
	public function suFindDisplay()
	{
		$mymodule = $this->suModule('table');
		$suModule = $this->suModule('raw');
		$mypath   = str_replace('/', '_', \dash\url::path());

		if( is_file(addons.'content_su/'.$suModule.'/model.php') && !$this->model_name)
		{
			$this->model_name = '\\addons\\content_su\\'.$suModule.'\model';
		}
		elseif( is_file(addons.'content_su/'.$mymodule.'/model.php')  && !$this->model_name)
		{
			$this->model_name = '\\addons\\content_su\\'.$mymodule.'\model';
		}


		switch ($suModule)
		{
			case 'home':
				break;

			case 'profile':
				//allow put on profile
				$this->display_name	= 'content_su/templates/module_profile.html';
				$this->get(null, 'datatable')->ALL($suModule);
				$this->put('profile')->ALL($suModule);
				break;

			// case 'permissions':
			// 	$this->display_name	= 'content_su/templates/module_permissions.html';
			// 	$this->get(null, 'datatable')->ALL('/^[^\/]*$/');
			// 	$this->put('permissions')->ALL();
			// 	break;

			case 'logout':
				\dash\redirect::to(null, false)->set_url('logout');
				break;
		}


		if( is_file(addons.'content_su/templates/static_'.$mypath.'.html') )
		{
			$this->display_name	= 'content_su/templates/static_'.$mypath.'.html';
		}
	}

	public function suModule($_resultType = null, $_module = null)
	{
		if($_module === null)
		{
			$_module = \dash\url::dir(0);
		}

		$myprefix = substr($_module, 0, -1);

		$result = ['raw' => $_module, 'table' => $_module, 'prefix' => $myprefix, 'type' => null, 'cat' => null ];
		switch ($_module)
		{

			case 'profile':
				$result['type']   = 'profile';
				$result['cat']    = 'profile';
				$result['table']  = 'options';
				$result['prefix'] = 'option';
				break;

			default:
				$result['type']   = $myprefix;
				break;
		}

		if(array_key_exists($_resultType, $result))
		{
			return $result[$_resultType];
		}
		else
		{
			return $result;
		}
	}

}
?>