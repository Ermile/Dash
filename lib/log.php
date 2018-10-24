<?php
namespace dash;


class log
{

	// check to not duplicate load notification file
	private static $load               = false;
	// list of project notifissin list
	private static $project_notif_list = [];
	// list of dash notification list
	private static $core_notif_list    = [];

	private static $temp_log = [];


	public static function temp_set($_caller, $_args)
	{
		self::$temp_log[$_caller] = $_args;
	}


	public static function save_temp()
	{
		foreach (self::$temp_log as $key => $value)
		{
			self::set($key, $value);
		}
		self::$temp_log = [];
	}


	public static function set($_caller, $_args = [])
	{

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
				case 'user_id':
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

		if($_args['user_id'] && is_numeric($_args['user_id']))
		{
			$user_id = $_args['user_id'];
		}
		else
		{
			$user_id = \dash\user::id();
		}

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

			$list1 = self::read_file(root.'/includes/log_caller/log.json');

			self::$project_notif_list = $list1;

			$dash_log_file_name = ['log', 'login', 'su', 'support', 'cp', 'account', 'hook'];

			foreach ($dash_log_file_name as $key => $value)
			{
				$list1 = self::read_file(core.'addons/includes/log_caller/'. $value. '.json');
				self::$core_notif_list    = array_merge(self::$core_notif_list, $list1);
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

}
?>