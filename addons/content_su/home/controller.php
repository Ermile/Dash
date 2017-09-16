<?php
namespace addons\content_su\home;
class controller extends \mvc\controller
{
	/**
	 * check login and permission
	 * @return [type] [description]
	 */
	function __construct()
	{
		parent::__construct();
	}

	function _permission($_content = null, $_module = null, $_perm = null,$_login = true)
	{
		// if user is not login then redirect
		if($_login && !$this->login())
		{
			\lib\debug::warn(T_("first of all, you must login to system!"));

			$mydomain = \lib\option::config('redirect_url');
			if($mydomain && $mydomain !== 'on')
			{
				$this->redirector($mydomain.'/enter?referer='.$_SERVER['REQUEST_SCHEME'] . '://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], false)->redirect();
			}
			else
			{
				$this->redirector(null, false)->set_domain()->set_url('enter')->redirect();
			}
		}

		// if content is not set then
		if($_content === null)
		{
			$_content = \lib\router::get_sub_domain();
		}

		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		if(Tld === 'dev')
		{
			// on tld dev open the su to upgrade for test
		}
		else
		{
			if(\lib\permission::access('su'))
			{
				// the user have permission of su
			}
			else
			{
				\lib\error::access(T_("Can not access to su"));
			}
		}
	}


	protected function _exception()
	{
		// run if get is set and no database exist
		if($this->suModule('raw') == 'install'
			&& \lib\utility::get('time') == 'first_time'
			&& !\lib\db::count_table()
		)
		{
			require_once(lib."install.php");
			\lib\main::$controller->_processor(['force_stop' => true, 'force_json' => false]);
		}
	}


	function _route()
	{
		// do for exception url
		self::_exception();
		// check permission
		self::_permission();

		// Restrict unwanted module
		if(!$this->suModlueList())
		{
			\lib\error::page(T_("Not found!"));
		}

		// Restrict unwanted child
		// if($mychild && !($mychild=='add' || $mychild=='edit' || $mychild=='delete' || $mychild=='list' || $mychild=='options'))
		// 	\lib\error::page(T_("Not found!"));
		$this->suFindDisplay();
	}


	/**
	 * find best display for this page!
	 * @return [type] [description]
	 */
	function suFindDisplay()
	{
		$mymodule = $this->suModule('table');
		$suModule = $this->suModule('raw');
		$mychild  = $this->child();
		$mypath   = $this->url('path','_');

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
				$mydomain = AccountService? AccountService.MainTld: null;
				$this->redirector(null, false)->set_domain($mydomain)->set_url('logout')->redirect();
				break;
		}


		if( is_file(addons.'content_su/templates/static_'.$mypath.'.html') )
		{
			$this->display_name	= 'content_su/templates/static_'.$mypath.'.html';
		}
	}


	// if url is outside of our list, return false else if valid module return true
	public function suModlueList($_module = null)
	{
		// return true;
		$mylist	= array_keys(self::$manifest['modules']->get_modules());
		if($_module == 'all')
		{
			return $mylist;
		}
		elseif($_module == 'permissions')
		{
			$mylist	= array_keys(self::$manifest['modules']->modules_search('permissions'));

			return $mylist;
		}

		$_module 	= $_module? $_module: $this->module();
		if(in_array($_module, $mylist))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function suModule($_resultType = null, $_module = null)
	{
		if($_module === null)
			$_module = $this->module();

		$myprefix = substr($_module, 0, -1);

		$result = ['raw' => $_module, 'table' => $_module, 'prefix' => $myprefix, 'type' => null, 'cat' => null ];
		switch ($_module)
		{
			case 'posts':
				$result['type']   = 'post';
				$result['cat']    = 'cat';

			case 'pages':
				$result['type']   = $result['type']? $result['type']: 'page';
				$result['cat']    = $result['cat']?  $result['cat']:  'cat';

			case 'helps':
				$result['type']   = $result['type']? $result['type']: 'help';
				$result['cat']    = $result['cat']?  $result['cat']:  'cat_help';

			case 'attachments':
				$result['type']   = $result['type']? $result['type']: 'attachment';
				$result['cat']    = $result['cat']?  $result['cat']:  'cat_file';

			case 'polls':
				$result['type']   = $result['type']? $result['type']: 'poll';
				$result['cat']    = $result['cat']?  $result['cat']:  'cat_poll';

			case 'books':
				$result['type']   = $result['type']? $result['type']: 'book';
				$result['cat']    = $result['cat']?  $result['cat']:  'cat_book';

			case 'socialnetwork':
				$result['type']   = $result['type']? $result['type']: 'socialnetwork';

				$result['table']  = 'posts';
				$result['prefix'] = 'post';
				break;

			case 'categories':
				$result['type']   = 'cat';
			case 'filecategories':
				$result['type']   = $result['type']? $result['type']: 'cat_file';
			case 'helpcategories':
				$result['type']   = $result['type']? $result['type']: 'cat_help';
			case 'pollcategories':
				$result['type']   = $result['type']? $result['type']: 'cat_poll';
			case 'bookcategories':
				$result['type']   = $result['type']? $result['type']: 'cat_book';
			case 'tags':
				$result['type']   = $result['type']? $result['type']: 'tag';

				$result['table']  = 'terms';
				$result['prefix'] = 'term';
				break;

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


	/**
	 * define perm modules for permission level
	 * @return [array] return the permissions in this content
	 */
	static function permModules()
	{
		$mylist	= self::$manifest['modules']->modules_search('permissions');
		return $mylist;
	}
}
?>