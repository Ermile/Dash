<?php
namespace dash;


class log
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


	// save log in database
	public static function db($_caller, $_args = [])
	{
		$default_args =
		[
			'status'        => 'enable',
			'data'          => null,
			'code'          => null,
			'send'          => null,
			'subdomain'     => \dash\url::subdomain(),
			'user_id'       => null,
			'notif'         => null,
			'user_idsender' => null,
			'readdate'      => null,
			'telegramdate'  => null,
			'smsdate'       => null,
			'emaildate'     => null,
			'meta'          => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		$user_id = \dash\user::id();

		if(!$user_id)
		{
			$user_id = null;
		}

		if($_args['data'])
		{
			$_args['data'] = \dash\safe::safe($_args['data'], 'raw');

			if(is_array($_args['data']) || is_object($_args['data']))
			{
				$_args['data'] = json_encode($_args['data'], JSON_UNESCAPED_UNICODE);
			}
		}
		else
		{
			$_args['data'] = null;
		}

		if($_args['meta'])
		{
			$_args['meta'] = \dash\safe::safe($_args['meta'], 'raw');

			if(is_array($_args['meta']) || is_object($_args['meta']))
			{
				$_args['meta'] = json_encode($_args['meta'], JSON_UNESCAPED_UNICODE);
			}
		}
		else
		{
			$_args['meta'] = null;
		}

		if($_caller && mb_strlen($_caller) >= 200)
		{
			$_caller = substr($_caller, 0, 198);
		}

		if($_args['code'] && mb_strlen($_args['code']) >= 200)
		{
			$_args['code'] = substr($_args['code'], 0, 198);
		}

		$insert_log =
		[
			'caller'        => $_caller,
			'user_id'       => $user_id,
			'datecreated'   => date("Y-m-d H:i:s"),
			'subdomain'     => $_args['subdomain'],
			'visitor_id'    => \dash\utility\visitor::id(),
			'data'          => $_args['data'],
			'status'        => $_args['status'],
			'code'          => $_args['code'],
			'send'          => $_args['send'],
			'notif'         => $_args['notif'],
			'user_idsender' => $_args['user_idsender'],
			'readdate'      => $_args['readdate'],
			'telegramdate'  => $_args['telegramdate'],
			'smsdate'       => $_args['smsdate'],
			'emaildate'     => $_args['emaildate'],
			'meta'          => $_args['meta'],
		];

		foreach ($insert_log as $key => $value)
		{
			if($value === null)
			{
				unset($insert_log[$key]);
			}
		}

		if(!empty($insert_log))
		{
			return \dash\db\logs::insert($insert_log);
		}
		return false;
	}



	public static function set($_caller, $_args = [])
	{
		self::clean();

		$all_list = self::lists();

		if(!isset($all_list[$_caller]))
		{
			return self::db(...func_get_args());
		}

		$caller_detail = $all_list[$_caller];

		if(!is_array($_args))
		{
			$_args = [];
		}


		$data  = [];
		$field = [];

		foreach ($caller_detail as $key => $value)
		{
			switch ($key)
			{
				case 'notification':
					$field['notif'] = $value;
					break;

				default:

					break;
			}
		}

		foreach ($_args as $key => $value)
		{
			switch ($key)
			{
				case 'subdomain':
				case 'status':
				case 'code':
				case 'send':
				case 'notif':
				case 'user_idsender':
				case 'readdate':
				case 'telegramdate':
				case 'smsdate':
				case 'emaildate':
				case 'meta':
					$field[$key] = $value;
					break;

				default:
					$data[$key] = $value;
					break;
			}
		}

		$new_args         = $field;
		$new_args['data'] = $data;

		return self::db($_caller, $new_args);
	}

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

		if(isset($_option['user_idsender']) && is_numeric($_option['user_idsender']))
		{
			$add_notif['user_idsender'] = $_option['user_idsender'];
		}

		if(isset($_option['meta']) && is_string($_option['meta']))
		{
			$add_notif['meta'] = $_option['meta'];
		}

		if(\dash\url::subdomain())
		{
			$add_notif['subdomain'] = \dash\url::subdomain();
		}

		$status = self::detail('status');
		if(!$status)
		{
			$status = 'enable';
		}

		$add_notif['caller'] = $_caller;
		$add_notif['notif']  = 1;
		$add_notif['status'] = $status;

		$save = self::save_notification_record($add_notif);
		return $save;
	}


	private static function save_notification_record($_detail)
	{
		$user_detail = self::$all_user_detail;

		$add_notif      = [];

		foreach ($user_detail as $key => $value)
		{
			$temp_add_notif      = $_detail;

			if(!isset($value['id']))
			{
				continue;
			}

			$temp_add_notif['user_id'] = $value['id'];

			$add_notif[] = $temp_add_notif;
		}

		if(!empty($add_notif))
		{
			$result = \dash\db\logs::multi_insert($add_notif);
		}

		return false;
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



}
?>