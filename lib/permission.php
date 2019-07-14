<?php
namespace dash;

/** Access: handle permissions **/
class permission
{
	// check to not duplicate load permission file
	private static $load                    = false;
	// check to not duplicate load user data
	private static $user_loaded             = false;
	// user permissio as a group name
	private static $user_permission         = null;
	// loaded user group permission and find whate containg of the user
	private static $user_permission_contain = [];
	// list of project permissin list
	private static $project_perm_list       = [];
	// list of project permission group
	private static $project_group           = [];
	// list of dash permission list
	private static $core_perm_list          = [];
	// list of dash permission group
	private static $core_group              = [];


	// read permission file and json_decode to make an array of it
	public static function read_file($_addr)
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


	// load all permission file and if exist lib\permission check this list by this function
	private static function load()
	{
		if(!self::$load)
		{
			self::$load              = true;
			self::$project_perm_list = self::read_file(root.'/includes/permission/list.json');
			self::$project_group     = self::read_file(root.'/includes/permission/group.me.json');

			self::$core_perm_list    = self::read_file(core.'addons/includes/permission/list.json');

			if(!self::$project_group)
			{
				self::$core_group        = self::read_file(core.'addons/includes/permission/group.json');
			}

			if(empty(self::$core_group))
			{
				self::$core_group['admin'] = ["title" => "admin"];
			}

			if(is_callable(['\lib\permission', 'perm_list']))
			{
				self::$project_perm_list = \lib\permission::perm_list(self::$project_perm_list, 'project');
				self::$core_perm_list    = \lib\permission::perm_list(self::$core_perm_list, 'dash');
			}

			if(is_callable(['\lib\permission', 'group_list']))
			{
				self::$project_group = \lib\permission::group_list(self::$project_group, 'project');
				self::$core_group    = \lib\permission::group_list(self::$core_group, 'dash');
			}
		}
	}


	public static function who_have($_caller, $_admin = true)
	{
		if($_admin)
		{
			$perm_name = ['admin'];
		}
		else
		{
			$perm_name = [];
		}

		$group     = self::groups();
		foreach ($group as $key => $value)
		{
			if(isset($value['contain']) && is_array($value['contain']))
			{
				if(in_array($_caller, $value['contain']))
				{
					array_push($perm_name, $key);
				}
			}
		}
		return $perm_name;

	}


	// delete permission from project file
	public static function delete_permission($_id)
	{
		$user_count = self::usercount();
		if(isset($user_count[$_id]) && intval($user_count[$_id]) > 0)
		{
			\dash\notif::error(T_("Someone have this permission!"). ' '. T_("Can not remove it."));
			return false;
		}

		if($_id === 'admin')
		{
			\dash\notif::error(T_("Can not remove admin!"));
			return false;
		}

		self::load();

		$new = self::groups();

		unset($new[$_id]);
		if(is_callable(['\lib\permission', 'delete_permission']))
		{
			$result = \lib\permission::delete_permission($new);
			if($result === null)
			{
				$new = json_encode($new, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
				\dash\file::write(root.'/includes/permission/group.me.json', $new);
			}
		}
		else
		{
			$new = json_encode($new, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			\dash\file::write(root.'/includes/permission/group.me.json', $new);
		}

		\dash\log::set('permissionDelete', ['code' => $_id]);
		\dash\notif::warn(T_("Permission removed"));
		return true;
	}


	// get count of user by permission group
	public static function usercount()
	{
		if(is_callable(['\lib\permission', 'usercount']))
		{
			$count = \lib\permission::usercount();
			if($count === null)
			{
				$count = \dash\db\users::permission_group();
			}
		}
		else
		{
			$count = \dash\db\users::permission_group();
		}

		$group = self::groups();

		$new_count = [];

		foreach ($group as $key => $value)
		{
			if(array_key_exists($key, $count))
			{
				$new_count[$key] = intval($count[$key]);
			}
			else
			{
				$new_count[$key] = 0;
			}
		}

		return $new_count;
	}


	// show all group name
	public static function groups($_project = false)
	{
		self::load();

		$all_group = [];

		if($_project)
		{
			$all_group = self::$project_group;
		}
		else
		{
			if(is_array(self::$core_group) && is_array(self::$project_group))
			{
				$all_group = array_merge(self::$core_group, self::$project_group);
			}
		}

		return $all_group;
	}


	// show all permission list
	public static function lists($_project = false)
	{
		self::load();

		$all_list = [];

		if($_project)
		{
			$all_list = self::$project_perm_list;
		}
		else
		{
			if(is_array(self::$core_perm_list) && is_array(self::$project_perm_list))
			{
				$all_list = array_merge(self::$core_perm_list, self::$project_perm_list);
			}
		}
		return $all_list;
	}


	public static function list_raw_project()
	{
		self::load();
		return self::$project_perm_list;
	}


	public static function list_raw_dash()
	{
		self::load();
		return self::$core_perm_list;
	}


	// make an array to draw permission list in quick view
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


	// save permission if file of every where
	public static function save_permission($_name, $_lable, $_contain, $_update = false)
	{
		self::load();

		if(!$_name)
		{
			\dash\notif::error(T_("Please set a valid name for permission"), 'name');
			return false;
		}

		$groups = self::groups();

		if(!$_update)
		{
			$_name = \dash\utility\filter::slug($_name);

			if(array_key_exists($_name, $groups))
			{
				\dash\notif::error(T_("This key was reserved, Try another"), 'name');
				return false;
			}

			if(!$_lable)
			{
				$_lable = $_name;
			}

			if($_name === 'supervisor' || $_name === 'admin')
			{
				\dash\notif::error(T_("This key was reserved, Try another"), 'name');
				return false;
			}
		}
		else
		{
			if($_name === 'supervisor' || $_name === 'admin')
			{
				\dash\notif::warn(T_("Can not change admin permission"));
				return false;
			}

			if(!array_key_exists($_name, $groups))
			{
				\dash\notif::error(T_("This key was not found in your permission list!"), 'name');
				return false;
			}
		}

		if(is_numeric($_name))
		{
			\dash\notif::error(T_("The title must include at least one alphabetical character"), 'name');
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

		if(!is_array($_contain))
		{
			\dash\notif::error(T_("Invalid contain of permission"));
			return false;
		}

		$all_list = self::lists();

		foreach ($_contain as $key => $value)
		{
			if(!array_key_exists($value, $all_list))
			{
				\dash\notif::error(T_("This item is not in your permission list!"));
				return false;
			}
		}

		// $new = self::$project_group;
		$new = self::groups();
		$new = array_merge($new, [$_name => ['title' => $_lable, 'contain' => $_contain]]);

		$saveInFile = false;

		if(is_callable(['\lib\permission', 'save_permission']))
		{
			$result = \lib\permission::save_permission($new);
			if($result === null)
			{
				$saveInFile = true;
			}
		}
		else
		{
			$saveInFile = true;
		}

		if($saveInFile)
		{
			$jsonMeAddr = root.'/includes/permission/group.me.json';
			$new = json_encode($new, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			\dash\file::write($jsonMeAddr, $new);
		}

		\dash\log::set('permissionAddNew', ['code' => $_name]);

		\dash\notif::ok(T_("Permission saved"));
		return true;
	}


	// load user data
	private static function load_user($_user_id, $_force = false)
	{
		if($_user_id && is_numeric($_user_id))
		{
			$user_id = $_user_id;
		}
		else
		{
			$user_id = \dash\user::id();
		}

		if(!self::$user_loaded)
		{
			self::$user_loaded     = true;
			self::$user_permission = \dash\user::detail('permission');
		}

		if($_force)
		{
			$user_detail = \dash\db\users::get_by_id($user_id);

			self::$user_permission = null;

			if(isset($user_detail['permission']))
			{
				self::$user_permission = $user_detail['permission'];
			}
		}

		if(is_callable(['\lib\permission', 'load_user']))
		{
			\lib\permission::load_user($_user_id, $_force);
		}
	}


	// opern permission for edit or delete
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


	// check the user is supervisor or not
	public static function supervisor($_force_load_user = false)
	{
		self::load_user(null, $_force_load_user);

		if(self::$user_permission === 'supervisor')
		{
			return true;
		}

		return false;
	}

	// for test and debug permission caller
	private static $test_mode = false;
	private static $dump      = false;
	private static $test_access = [];

	public static function test_access($_caller = null, $_dump = false)
	{
		if(\dash\url::isLocal())
		{
			self::$test_mode     = true;
			if(!self::$dump && $_dump)
			{
				self::$dump          = $_dump;
			}
			self::$test_access[] = $_caller;
		}
	}


	private static function check_test($_caller)
	{
		if(in_array($_caller, self::$test_access))
		{
			return true;
		}
		else
		{
			if(self::$dump)
			{
				\dash\code::dump($_caller);
			}
			return false;
		}
	}


	// check permission
	public static function check($_caller, $_user_id = null)
	{
		// for test and debug permission
		if(self::$test_mode)
		{
			return self::check_test($_caller);
		}

		self::load_user($_user_id);

		if(self::supervisor(false))
		{
			return true;
		}

		$all_list = self::lists();
		if(array_key_exists($_caller, $all_list))
		{
			if(isset($all_list[$_caller]['check']) && $all_list[$_caller]['check'])
			{
				self::load_user($_user_id, true);
			}
		}

		if(is_callable(['\lib\permission', 'plan']))
		{
			$check_plan = \lib\permission::plan($_caller);
			if($check_plan === false)
			{
				return false;
			}
			else
			{
				if(is_callable(['\lib\permission', 'check']))
				{
					$check_advance_perm = \lib\permission::check($_caller);

					if($check_advance_perm === false)
					{
						return false;
					}
					elseif($check_advance_perm === true)
					{
						return true;
					}
				}
			}
		}


		if(self::$user_permission === 'admin')
		{
			return true;
		}

		$all_contain = self::groups();

		if(isset($all_contain[self::$user_permission]['contain']))
		{
			if(is_array($all_contain[self::$user_permission]['contain']) && in_array($_caller, $all_contain[self::$user_permission]['contain']))
			{
				return true;
			}
		}

		return false;
	}


	// check access
	public static function access($_caller, $_msg = null)
	{
		$check = self::check($_caller);

		if(!$check)
		{
			if(!$_msg)
			{
				$_msg = T_("Permission denied");
			}

			\dash\header::status(403, $_msg);
		}
		return true;
	}
}
?>