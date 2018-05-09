<?php
namespace dash;

/** Access: handle permissions **/
class permission
{
	private static $load                    = false;
	private static $user_loaded             = false;
	private static $user_permission         = null;

	private static $project_perm_list       = [];
	private static $project_group           = [];

	private static $core_perm_list          = [];
	private static $core_group              = [];

	private static $user_permission_contain = [];


	public static function write_file($_caller, $_postion)
	{
		self::load();

		$check_list = [];

		if($_postion === 'dash')
		{
			$check_list = self::$core_perm_list;
			$addr       = core.'addons/includes/permission/list.json';
		}
		elseif($_postion === 'project')
		{
			$check_list = self::$project_perm_list;
			$addr       = root.'/includes/permission/list.json';
		}
		else
		{
			return;
		}


		foreach ($check_list as $key => $value)
		{
			if(!in_array($key, $_caller))
			{
				unset($check_list[$key]);
			}
		}

		foreach ($_caller as $key => $value)
		{
			if(!array_key_exists($value, $check_list))
			{
				$check_list[$value] =
				[
					'title'   => T_($value),
					'cat'     => null,
					// 'check'   => false,
					// 'verify'  => false,
					// 'require' => [],
				];
			}
		}

		$check_list = json_encode($check_list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		\dash\file::write($addr, $check_list);

	}


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
			self::$load              = true;
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
	}


	public static function supervisor($_force_load_user = true)
	{
		self::load_user(null, $_force_load_user);

		if(self::$user_permission === 'supervisor')
		{
			return true;
		}

		return false;
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


	public static function check($_caller, $_user_id = null)
	{
		self::load_user($_user_id);

		if(self::supervisor(false))
		{
			return true;
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
			if(in_array($_caller, $all_contain[self::$user_permission]['contain']))
			{
				return true;
			}
		}

		return false;
	}


	public static function access($_caller)
	{
		$check = self::check($_caller);

		if(!$check)
		{
			if(\dash\request::json_accept() || \dash\request::ajax())
			{
				\dash\notif::error(T_("Permission denied"));
				\dash\code::end();
			}
			else
			{
				\dash\header::status(403, T_("Permission denied"));
			}
		}
		return true;
	}
}
?>