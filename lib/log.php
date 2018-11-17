<?php
namespace dash;


class log
{
	private static $temp_log = [];


	public static function temp_set($_caller, $_args)
	{
		self::$temp_log[$_caller] = $_args;
	}


	public static function save_temp($_option = [])
	{
		foreach (self::$temp_log as $key => $value)
		{
			if(isset($_option['replace']) && is_array($_option['replace']))
			{
				self::set($key, array_merge($value, $_option['replace']));
			}
			else
			{
				self::set($key, $value);
			}
		}
		self::$temp_log = [];
	}


	private static function call_fn($_fn, $_args = [], $_namespace = null)
	{
		if(!$_namespace)
		{
			$_namespace = "\\lib\\app\\log\\caller\\$_fn";
		}

		$project_function = [$_namespace, $_fn];

		$dash_function    = [$_namespace, $_fn];

		if(is_callable($project_function))
		{
			$namespace       = $project_function[0];
			$function        = $project_function[1];
			$result_function = $namespace::$function($_args);
			return $result_function;
		}
		elseif(is_callable($dash_function))
		{
			$namespace       = $dash_function[0];
			$function        = $dash_function[1];
			$result_function = $namespace::$function($_args);
			return $result_function;
		}

		return null;
	}


	public static function set($_caller, $_args = [])
	{
		$data  = [];
		$field = [];

		if(!is_array($_args))
		{
			$_args = [$_args];
		}

		$before_add = self::call_fn('before_add', $_args);

		if(is_array($before_add))
		{
			$_args = array_merge($_args, $before_add);
		}

		foreach ($_args as $key => $value)
		{
			switch ($key)
			{
				case 'notif':
					$field['notif'] = self::call_fn('is_notif');
					break;
				case 'subdomain':
				case 'status':
				case 'code':
				case 'send':
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
			'subdomain'     => null,
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

		$log_id = \dash\db\logs::insert($insert_log);
		return $log_id;
	}
}
?>