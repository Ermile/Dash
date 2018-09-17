<?php
namespace dash;

/** handle notifications **/
class notification
{
	// check to not duplicate load notification file
	private static $load               = false;
	// check to not duplicate load user data
	private static $user_loaded        = false;
	// list of project notifissin list
	private static $project_notif_list = [];
	// list of dash notification list
	private static $core_notif_list    = [];

	private static $notif_detail       = [];

	private static $all_user_detail    = [];



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



	private static function clean()
	{
		self::$notif_detail    = [];
		self::$all_user_detail = [];
	}


	private static function detect_user($_user_id)
	{
		$all_user_detail = [];
		if($_user_id && is_numeric($_user_id))
		{
			// force send to this user
			if(intval($_user_id) === intval(\dash\user::id()))
			{
				// neddless to load userdetail again
				$all_user_detail[] = \dash\user::detail();
			}
			else
			{
				$find_user = \dash\db\users::get_by_id($_user_id);
				if(!$find_user)
				{
					return false;
				}
				$all_user_detail[] = $find_user;
			}
		}
		else
		{
			$send_to = self::detail('send_to');
			if(!$send_to || !is_array($send_to))
			{
				return false;
			}

			if(in_array('supervisor', $send_to))
			{
				$load_all_supervisor = \dash\db\users::get(['permission' => 'supervisor', 'status' => 'active']);
				if($load_all_supervisor)
				{
					$all_user_detail = array_merge($all_user_detail, $load_all_supervisor);
				}
			}

			if(in_array('admin', $send_to))
			{
				$load_all_admin = \dash\db\users::get(['permission' => 'admin', 'status' => 'active']);
				if($load_all_admin)
				{
					$all_user_detail = array_merge($all_user_detail, $load_all_admin);
				}
			}
		}

		if(empty($all_user_detail))
		{
			return false;
		}

		// to remove duplicate if exist
		$all_user_detail       = array_combine(array_column($all_user_detail, 'id'), $all_user_detail);
		self::$all_user_detail = $all_user_detail;
		return true;
	}

	private static function detail($_key = null, $_sub_key = null)
	{
		$detail = self::$notif_detail;
		if(!$detail)
		{
			return null;
		}

		if($_key)
		{
			if($_sub_key)
			{
				if(array_key_exists($_key, $detail))
				{
					if(is_array($detail[$_key]) && array_key_exists($_sub_key, $detail[$_key]))
					{
						return $detail[$_key][$_sub_key];
					}
					else
					{
						return null;
					}
				}
				else
				{
					return null;
				}
			}
			else
			{
				if(array_key_exists($_key, $detail))
				{
					return $detail[$_key];
				}
				else
				{
					return null;
				}
			}
		}
		else
		{
			return $detail;
		}
	}


	private static function detail_set($_key = null, $_sub_key = null, $_value = null)
	{
		$detail = self::$notif_detail;
		if(!$detail)
		{
			return null;
		}

		if($_key)
		{
			if($_sub_key)
			{
				if(array_key_exists($_key, $detail))
				{
					if(is_array($detail[$_key]) && array_key_exists($_sub_key, $detail[$_key]))
					{
						$detail[$_key][$_sub_key] = $_value;
					}
				}
			}
			else
			{
				if(array_key_exists($_key, $detail))
				{
					$detail[$_key] = $_value;
				}
			}
		}

		self::$notif_detail = $detail;
	}




	// send notification
	public static function send($_caller, $_user_id = null, $_replace = [], $_option = [])
	{
		self::clean();

		$all_list = self::lists();

		if(!isset($all_list[$_caller]))
		{
			return null;
		}

		self::$notif_detail = $notif_detail = $all_list[$_caller];

		$user_detail = self::detect_user($_user_id);

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
		$title = self::detail('title');

		if(isset($_option['title']))
		{
			$title = $_option['title'];
		}
		elseif($title)
		{
			$title = T_($title, $_replace);
		}

		// set content
		$content = self::detail('content');

		if(isset($_option['content']))
		{
			$content = $_option['content'];
		}
		elseif($content)
		{
			$content = T_($content, $_replace);
		}

		if(self::detail('send_msg', 'telegram'))
		{
			self::detail_set('send_msg', 'telegram', T_(self::detail('send_msg', 'telegram'), $_replace));
		}

		if(self::detail('send_msg', 'sms'))
		{
			self::detail_set('send_msg', 'sms', T_(self::detail('send_msg', 'sms'), $_replace));
		}

		if(self::detail('send_msg', 'email'))
		{
			self::detail_set('send_msg', 'email', T_(self::detail('send_msg', 'email'), $_replace));
		}

		$expiredate = null;
		$life_time = self::detail('life_time');

		if($life_time)
		{
			$life_time = self::calc_life_time($life_time);
			if(is_numeric($life_time))
			{
				$expiredate = date("Y-m-d H:i:s", intval( time() + intval($life_time) ));
			}
		}

		if(self::detail('category'))
		{
			$add_notif['category'] = self::detail('category');
		}


		if(self::detail('telegram'))
		{
			$add_notif['telegram'] = 1;
		}

		if(self::detail('sms'))
		{
			$add_notif['sms'] = 1;
		}

		if(self::detail('email'))
		{
			$add_notif['email'] = 1;
		}


		if(self::detail('need_answer'))
		{
			$add_notif['needanswer'] = 1;
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

		$save = self::save_notification_record($add_notif);
		return $save;
	}


	private static function save_notification_record($_detail)
	{
		$user_detail = self::$all_user_detail;

		$add_notif      = [];
		$add_send_notif = [];

		foreach ($user_detail as $key => $value)
		{
			$temp_add_notif      = $_detail;

			if(!isset($value['id']))
			{
				continue;
			}

			if(isset($_detail['telegram']) && $_detail['telegram'] && isset($value['chatid']) && $value['chatid'])
			{
				$temp_add_send_notif           = [];
				$temp_add_send_notif['way']    = 'telegram';
				$temp_add_send_notif['status'] = 'awaiting';
				$temp_add_send_notif['to']     = $value['chatid'];

				if(self::detail('send_msg', 'telegram'))
				{
					$temp_add_send_notif['text']        = self::detail('send_msg', 'telegram');
					$temp_add_send_notif['datecreated'] = date("Y-m-d H:i:s");
					$add_send_notif[]                   = $temp_add_send_notif;
				}
			}

			if(isset($_detail['sms']) && $_detail['sms'] && isset($value['mobile']) && \dash\utility\filter::mobile($value['mobile']))
			{
				$temp_add_send_notif           = [];
				$temp_add_send_notif['way']    = 'sms';
				$temp_add_send_notif['status'] = 'awaiting';
				$temp_add_send_notif['to']     = $value['mobile'];

				if(self::detail('send_msg', 'sms'))
				{
					$temp_add_send_notif['text']        = self::detail('send_msg', 'sms');
					$temp_add_send_notif['datecreated'] = date("Y-m-d H:i:s");
					$add_send_notif[]                   = $temp_add_send_notif;
				}
			}

			if(isset($_detail['email']) && $_detail['email'] && isset($value['email']) && \dash\utility\filter::email($value['email']))
			{
				$temp_add_send_notif           = [];
				$temp_add_send_notif['way']    = 'email';
				$temp_add_send_notif['status'] = 'awaiting';
				$temp_add_send_notif['to']     = $value['email'];

				if(self::detail('send_msg', 'email'))
				{
					$temp_add_send_notif['text']        = self::detail('send_msg', 'email');
					$temp_add_send_notif['datecreated'] = date("Y-m-d H:i:s");
					$add_send_notif[]                   = $temp_add_send_notif;
				}
			}

			$temp_add_notif['user_id'] = $value['id'];

			$add_notif[] = $temp_add_notif;
		}

		if(!empty($add_notif))
		{
			$result = \dash\db\notifications::multi_insert($add_notif);
		}

		if(!empty($add_send_notif))
		{
			$result = \dash\db\sendnotifications::multi_insert($add_send_notif);
		}

		return false;
	}
}
?>