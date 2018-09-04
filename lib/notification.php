<?php
namespace dash;

/** handle notifications **/
class notification
{
	// check to not duplicate load notification file
	private static $load                    = false;
	// check to not duplicate load user data
	private static $user_loaded             = false;
	// user notifissio as a group name
	private static $user_notification         = null;
	// loaded user group notification and find whate containg of the user
	private static $user_notification_contain = [];
	// list of project notifissin list
	private static $project_notif_list       = [];
	// list of dash notification list
	private static $core_notif_list          = [];



	// read notification file and json_decode to make an array of it
	public static function read_file($_addr)
	{
		$notif = [];

		if(is_file($_addr))
		{
			$notif = \dash\file::read($_addr);
			$notif = json_decode($notif, true);
			if(!is_array($notif))
			{
				$notif = [];
			}
		}
		return $notif;
	}


	// load all notification file and if exist lib\notification check this list by this function
	private static function load()
	{
		if(!self::$load)
		{
			self::$load               = true;
			self::$project_notif_list = self::read_file(root.'/includes/notification/list.json');
			self::$core_notif_list    = self::read_file(core.'addons/includes/notification/list.json');

			if(is_callable(['\lib\notification', 'notif_list']))
			{
				self::$project_notif_list = \lib\notification::notif_list(self::$project_notif_list, 'project');
				self::$core_notif_list    = \lib\notification::notif_list(self::$core_notif_list, 'dash');
			}
		}
	}


	// show all notification list
	public static function lists($_project = false)
	{
		self::load();

		$all_list = [];

		if($_project)
		{
			$all_list = self::$project_notif_list;
		}
		else
		{
			if(is_array(self::$core_notif_list) && is_array(self::$project_notif_list))
			{
				$all_list = array_merge(self::$core_notif_list, self::$project_notif_list);
			}
		}
		return $all_list;
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
			self::$user_loaded       = true;
			self::$user_notification = \dash\user::detail('notification');
		}

		if($_force)
		{
			$user_detail = \dash\db\users::get_by_id($user_id);

			self::$user_notification = null;

			if(isset($user_detail['notification']))
			{
				self::$user_notification = $user_detail['notification'];
			}
		}

		if(is_callable(['\lib\notification', 'load_user']))
		{
			\lib\notification::load_user($_user_id, $_force);
		}
	}


	// opern notification for edit or delete
	public static function load_notification($_id)
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


	// send notification
	public static function send($_caller, $_user_id = null, $_replace = [], $_option = [])
	{
		self::load_user($_user_id);

		$all_list = self::lists();

		if(is_callable(['\lib\notification', 'send']))
		{
			$check_advance_notif = \lib\notification::send($_caller, $_user_id, $_replace, $_option);

			if($check_advance_notif === false)
			{
				return false;
			}
			elseif($check_advance_notif === true)
			{
				return true;
			}
		}

		return true;
	}
}
?>