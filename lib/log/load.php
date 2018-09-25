<?php
namespace dash\log;

trait load
{

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

			$list1 = self::read_file(root.'/includes/log_notification/log.json');
			$list2 = self::read_file(root.'/includes/log_notification/notification.json');

			self::$project_notif_list = array_merge($list1, $list2);

			$list1 = self::read_file(core.'addons/includes/log_notification/log.json');
			$list2 = self::read_file(core.'addons/includes/log_notification/notification.json');

			self::$core_notif_list    = array_merge($list1, $list2);

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


	private static function clean()
	{
		self::$notif_detail    = [];
		self::$all_user_detail = [];
	}

}
?>
