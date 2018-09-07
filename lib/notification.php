<?php
namespace dash;

/** handle notifications **/
class notification
{
	// check to not duplicate load notification file
	private static $load               = false;
	// check to not duplicate load user data
	private static $user_loaded        = false;
	// user notifissio as a group name
	private static $user_detail        = null;
	// list of project notifissin list
	private static $project_notif_list = [];
	// list of dash notification list
	private static $core_notif_list    = [];



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
	private static function user_detail($_user_id, $_key = null, $_value = null)
	{
		if(!$_user_id)
		{
			$_user_id = \dash\user::id();
		}

		if(!is_numeric($_user_id))
		{
			return false;
		}

		if(!self::$user_loaded)
		{
			self::$user_loaded       = true;
			if(intval(\dash\user::id()) !== intval(\dash\user::id()))
			{
				$user_detail = \dash\db\users::get_by_id($user_id);
			}
			else
			{
				$user_detail = \dash\user::detail();
			}

			self::$user_detail = $user_detail;
		}

		return self::$user_detail;
	}


	private static function get_detail($_detail, $_key)
	{
		if(is_array($_detail))
		{
			if(array_key_exists($_key, $_detail))
			{
				return $_detail[$_key];
			}
			else
			{
				return null;
			}
		}
		return null;
	}

	private static function calc_life_time($_life_time)
	{
		if(preg_match("/^(\d+)([YmdHis])$/", $_life_time, $split))
		{
			$life_time = intval($split[1]);

			switch ($split[2])
			{
				case 'Y':
					$life_time *= 12;
				case 'm':
					$life_time *= 30;
				case 'd':
					$life_time *= 24;
				case 'H':
					$life_time *= 60;
				case 'i':
					$life_time *= 60;
				case 's':
					$life_time *= 1;
					break;
			}

			return $life_time;
		}
		else
		{
			if(is_numeric($_life_time))
			{
				return intval($_life_time);
			}
		}
	}


	// send notification
	public static function send($_caller, $_user_id = null, $_replace = [], $_option = [])
	{
		$all_list = self::lists();

		if(!isset($all_list[$_caller]))
		{
			return null;
		}

		$notif_detail = $all_list[$_caller];

		$user_detail = self::user_detail($_user_id);

		if($user_detail === false)
		{
			return false;
		}

		if(!is_array($_replace))
		{
			$_replace = [];
		}

		if(!is_array($_option))
		{
			$_option = [];
		}

		$add_notif = [];
		$sendnotif = [];

		// set title
		$title = self::get_detail($notif_detail, 'title');

		if(isset($_option['title']))
		{
			$title = $_option['title'];
		}
		elseif($title)
		{
			$title = T_($title, $_replace);
		}

		// set content
		$content = self::get_detail($notif_detail, 'content');

		if(isset($_option['content']))
		{
			$content = $_option['content'];
		}
		elseif($content)
		{
			$content = T_($content, $_replace);
		}

		$expiredate = null;
		$life_time = self::get_detail($notif_detail, 'life_time');

		if($life_time)
		{
			$life_time = self::calc_life_time($life_time);
			if(is_numeric($life_time))
			{
				$expiredate = date("Y-m-d H:i:s", intval( time() + intval($life_time) ));
			}
		}

		if(isset($user_detail['chatid']) && $user_detail['chatid'])
		{
			if(self::get_detail($notif_detail, 'telegram'))
			{
				$sendnotif['telegram'] = ['to' => $user_detail['chatid']];
				$add_notif['telegram'] = 1;
			}
		}

		if(isset($user_detail['mobile']) && \dash\utility\filter::mobile($user_detail['mobile']))
		{
			if(self::get_detail($notif_detail, 'sms'))
			{
				$sendnotif['sms'] = ['to' => $user_detail['mobile']];
				$add_notif['sms'] = 1;
			}
		}

		if(isset($user_detail['email']) && filter_var($user_detail['email'], FILTER_VALIDATE_EMAIL))
		{
			if(self::get_detail($notif_detail, 'email'))
			{
				$sendnotif['email'] = ['to' => $user_detail['mobile']];
				$add_notif['email'] = 1;
			}
		}

		if(self::get_detail($notif_detail, 'need_answer'))
		{
			$add_notif['needanswer'] = 1;
		}

		// @check supervisor or admin to load all user
		if(isset($user_detail['id']) && is_numeric($user_detail['id']))
		{
			$add_notif['user_id'] = $user_detail['id'];
		}

		if(isset($_option['url']) && is_numeric($_option['url']))
		{
			$add_notif['url'] = $_option['url'];
		}

		if(isset($_option['user_idsender']) && is_numeric($_option['user_idsender']))
		{
			$add_notif['user_idsender'] = $_option['user_idsender'];
		}

		if(isset($_option['related_foreign']))
		{
			$add_notif['related_foreign'] = $_option['related_foreign'];
		}

		if(isset($_option['related_id']) && is_numeric($_option['related_id']))
		{
			$add_notif['related_id'] = $_option['related_id'];
		}

		if(isset($_option['desc']) && is_string($_option['desc']))
		{
			$add_notif['desc'] = $_option['desc'];
		}

		if(isset($_option['meta']) && is_string($_option['meta']))
		{
			$add_notif['meta'] = $_option['meta'];
		}

		if(\dash\url::subdomain())
		{
			$add_notif['subdomain'] = \dash\url::subdomain();
		}

		$add_notif['status']     = 'awaiting';
		$add_notif['title']      = $title;
		$add_notif['caller']     = $_caller;
		$add_notif['content']    = $content;
		$add_notif['expiredate'] = $expiredate;

		if(\dash\db\notifications::insert($add_notif))
		{
			if(!empty($sendnotif))
			{
				$add_send_notif = [];
				foreach ($sendnotif as $key => $value)
				{
					$add_send_notif[]  =
					[
						'to'          => $value['to'],
						'way'         => $key,
						'status'      => 'awaiting',
						'text'        => "$add_notif[title] \n $add_notif[content]",
						'datecreated' => date("Y-m-d H:i:s"),
					];
				}

				if(!empty($add_send_notif))
				{
					\dash\db\sendnotifications::multi_insert($add_send_notif);
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>