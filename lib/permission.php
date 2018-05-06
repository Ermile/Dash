<?php
namespace dash;

/** Access: handle permissions **/
class permission
{
	private static $load              = false;
	private static $project_perm_list = [];
	private static $project_group     = [];
	private static $core_perm_list    = [];
	private static $core_group        = [];


	private static function read_file($_addr)
	{
		$perm = [];

		if(is_file($_addr))
		{
			$perm = \dash\file::read($_addr);
			$perm = json_decode($perm, true);
			if(!is_array($perm))
			{
				$perm = [];
			}
		}
		return $perm;
	}


	private static function load()
	{
		if(!self::$load)
		{
			self::$load        = true;
			self::$project_perm_list = self::read_file(root.'/includes/permission/list.json');
			self::$project_group     = self::read_file(root.'/includes/permission/group.me.json');
			self::$core_perm_list    = self::read_file(core.'addons/includes/permission/list.json');
			self::$core_group        = self::read_file(core.'addons/includes/permission/group.json');
		}
	}


	public static function groups($_project = false)
	{
		self::load();
		if($_project)
		{
			$all_group = self::$project_group;
		}
		else
		{
			$all_group = array_merge(self::$core_group, self::$project_group);
		}
		return $all_group;
	}


	public static function categorize_list()
	{
		self::load();

		$result   = [];
		$core_cat = [];

		foreach (self::$core_perm_list as $key => $value)
		{
			if(!isset($core_cat[$value['cat']]))
			{
				$core_cat[$value['cat']] = [];
			}

			$core_cat[$value['cat']][$key] = $value;
		}

		$result['dash'] = $core_cat;

		$project_cat = [];

		foreach (self::$project_perm_list as $key => $value)
		{
			if(!isset($project_cat[$value['cat']]))
			{
				$project_cat[$value['cat']] = [];
			}

			$project_cat[$value['cat']][$key] = $value;
		}

		$result['project'] = $project_cat;

		return $result;
	}


	public static function save_permission($_name, $_lable, $_contain, $_update = false)
	{
		self::load();

		$_name = \dash\utility\filter::slug($_name);

		if(!$_update)
		{
			if(array_key_exists($_name, self::$project_group))
			{
				\dash\notif::error(T_("This key was reserved, Try another"), 'name');
				return false;
			}
		}

		if($_name === 'supervisor' || $_name === 'admin')
		{
			\dash\notif::error(T_("This key was reserved, Try another"), 'name');
			return false;
		}

		if(mb_strlen($_name) > 30)
		{
			\dash\notif::error(T_("Name too large, Try another"), 'name');
			return false;
		}


		if(mb_strlen($_lable) > 30)
		{
			\dash\notif::error(T_("Label too large, Try another"), 'label');
			return false;
		}

		$new = self::$project_group;
		$new = array_merge($new, [$_name => ['title' => $_lable, 'contain' => $_contain]]);
		$new = json_encode($new, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		\dash\file::write(root.'/includes/permission/group.me.json', $new);
		\dash\notif::ok(T_("Permission saved"));
		return true;
	}


	public static function load_permission($_id)
	{
		self::load();

		if(array_key_exists($_id, self::$project_group))
		{
			return self::$project_group[$_id];
		}

		if(array_key_exists($_id, self::$core_group))
		{
			return self::$core_group[$_id];
		}

		return false;

	}






















	public static $perm_list       = [];
	public static $user_id         = null;
	public static $caller          = null;
	public static $permission      = null;
	public static $force_load_user = false;

	/**
	 * load permission
	 */
	public static function _construct()
	{
		self::load();
		if(empty(self::$perm_list))
		{
			if(file_exists(root.'/includes/permission/permission.php'))
			{
				require_once(root.'/includes/permission/permission.php');
			}
			// cp perm list
			self::$perm_list[1]  = ['caller' => 'upload_1000_mb', 'title' => T_("upload_1000_mb"), 'cat' => 'cp'];
			self::$perm_list[2]  = ['caller' => 'upload_100_mb', 'title' => T_("upload_100_mb"), 'cat' => 'cp'];
			self::$perm_list[3]  = ['caller' => 'upload_10_mb', 'title' => T_("upload_10_mb"), 'cat' => 'cp'];
			self::$perm_list[4]  = ['caller' => 'admin:admin:view', 'title' => T_("admin:admin:view"), 'cat' => 'cp'];
			self::$perm_list[5]  = ['caller' => 'cp', 'title' => T_("cp"), 'cat' => 'cp'];
			self::$perm_list[6]  = ['caller' => 'cp:transaction:invoicedetails', 'title' => T_("cp:transaction:invoicedetails"), 'cat' => 'cp'];
			self::$perm_list[7]  = ['caller' => 'cp:transaction:invoices', 'title' => T_("cp:transaction:invoices"), 'cat' => 'cp'];
			self::$perm_list[8]  = ['caller' => 'cp:transaction:logs', 'title' => T_("cp:transaction:logs"), 'cat' => 'cp'];
			self::$perm_list[9]  = ['caller' => 'cp:transaction:notifications', 'title' => T_("cp:transaction:notifications"), 'cat' => 'cp'];
			self::$perm_list[10] = ['caller' => 'cp:permission:add', 'title' => T_("cp:permission:add"), 'cat' => 'cp'];
			self::$perm_list[11] = ['caller' => 'cp:transaction:add', 'title' => T_("cp:transaction:add"), 'cat' => 'cp'];
			self::$perm_list[12] = ['caller' => 'cp:transaction', 'title' => T_("cp:transaction"), 'cat' => 'cp'];
			self::$perm_list[13] = ['caller' => 'cp:user:add', 'title' => T_("cp:user:add"), 'cat' => 'cp'];
			self::$perm_list[14] = ['caller' => 'cp:user', 'title' => T_("cp:user"), 'cat' => 'cp'];
			self::$perm_list[15] = ['caller' => 'cp:user:detail', 'title' => T_("cp:user:detail"), 'cat' => 'cp'];
			self::$perm_list[16] = ['caller' => 'cp:user:edit', 'title' => T_("cp:user:edit"), 'cat' => 'cp'];
			self::$perm_list[17] = ['caller' => 'enter:another:session', 'title' => T_("enter:another:session"), 'cat' => 'cp'];
			// self::$perm_list[200] = ['caller' => '....', 'title' => T_("..."), 'cat' => 'cp'];
		}


		if(!self::$user_id)
		{
		 	if(isset($_SESSION['user']['id']) && is_numeric($_SESSION['user']['id']))
		 	{
				self::$user_id = $_SESSION['user']['id'];
		 	}
		 	else
			{
				self::$user_id = \dash\user::id();
			}
		}

		// set permission as static value if exist, but dont need
		self::load_user_data();
	}


	/**
	 * Loads an user data.
	 */
	private static function load_user_data()
	{
		// if permission is set before it, return true
		if(self::$permission)
		{
			return true;
		}
		// if permission is exist in session use it
		if(isset($_SESSION['user']['permission']) && !self::$force_load_user)
		{
			self::$permission = $_SESSION['user']['permission'];
		}
		// else if we have user_id get it from user detail
		else if(self::$user_id && is_numeric(self::$user_id))
		{
			$user_data = \dash\db\users::get_by_id(self::$user_id);
			if(isset($user_data['permission']))
			{
				self::$permission = trim($user_data['permission']);
				// $_SESSION['user']['permission'] = self::$permission;
			}
		}
	}

	/**
	 * check access users
	 *
	 * @param      <type>  $_caller  The caller
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function access($_caller, $_action = null, $_user_id = null)
	{
		// set the user id if user id send to this function and self::user_id not set
		if($_user_id && is_numeric($_user_id) && !self::$user_id)
		{
			self::$user_id = $_user_id;
			self::$force_load_user = true;
		}

		// load permission list and check session if self::$user_id not set
		self::_construct();

		// check permission
		$permission_check = self::check($_caller);

		if($_action === 'notify')
		{
			if($permission_check)
			{
				return true;
			}
			else
			{
				\dash\notif::error(T_("Can not access to it"));
				return false;
			}
		}
		elseif($_action === 'block')
		{
			if($permission_check)
			{
				return true;
			}
			else
			{
				\dash\header::status(403, T_("Access denied"));
				return false;
			}
		}
		else
		{
			return $permission_check;
		}
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_caller  The caller
	 */
	private static function check($_caller, $_admin_su = 'admin')
	{
		// the user not found!
		if(!self::$user_id)
		{
			return false;
		}
		// no permissin need in this project
		if(empty(self::$perm_list))
		{
			// if need su permission must be check it
			// never return true if need su and the user is not su!
			if($_admin_su === 'admin')
			{
				return true;
			}
		}

		self::caller($_caller);

		$user_data_loaded = false;
		if(isset(self::$caller['need_check']))
		{
			self::load_user_data();
			$user_data_loaded = true;
		}

		if(isset(self::$caller['need_verify']))
		{
			if(!$user_data_loaded)
			{
				self::load_user_data();
			}
			// and verify users !
		}

		// if need check su
		if($_admin_su === 'su')
		{
			// only supervisor can load this mudole
			if(self::$permission === 'supervisor')
			{
				return true;
			}
			else
			{
				// never return true if need su and the user is not su!
				return false;
			}
		}

		// admin use -f!
		if(self::$permission === 'admin' || self::$permission === 'supervisor')
		{
			return true;
		}

		// if permission is not null and exist, explode it
		if(self::$permission && is_string(self::$permission))
		{
			$explode = explode(',', self::$permission);

			if(isset(self::$caller['key']))
			{
				if(in_array(self::$caller['key'], $explode))
				{
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_caller  The caller
	 */
	private static function caller($_caller)
	{
		$caller              = array_column(self::$perm_list, 'caller');
		$caller              = array_combine(array_keys(self::$perm_list), $caller);
		$key                 = array_search($_caller, $caller);
		self::$caller        = isset(self::$perm_list[$key]) ? self::$perm_list[$key] : null;
		self::$caller['key'] = $key;
	}


	/**
	 * return the perm list
	 */
	public static function list($_group = null)
	{
		self::_construct();
		return self::$perm_list;
	}


	/**
	 * ACCESS TO SU CONTENT
	 * DANGER
	 * JUST THE DEVELOPERS CAN SEE THIS CONTENT
	 * NOT OTHER PEOPLE
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function access_su($_user_id = null)
	{
		// set the user id if user id send to this function and self::user_id not set
		if($_user_id && is_numeric($_user_id) && !self::$user_id)
		{
			self::$user_id = $_user_id;
		}

		self::$force_load_user = true;

		// load permission list and check session if self::$user_id not set
		self::_construct();

		// check permission
		$permission_check = self::check('su', 'su');

		return $permission_check;
	}
}
?>