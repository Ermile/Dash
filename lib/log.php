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


	private static function call_fn($_class, $_fn, $_args = [])
	{
		$project_function = ["\\lib\\app\\log\\caller\\$_class", $_fn];
		$dash_function    = ["\\dash\\app\\log\\caller\\$_class", $_fn];

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

		$before_add = self::call_fn($_caller, 'before_add', $_args);

		if(is_array($before_add))
		{
			$_args = array_merge($_args, $before_add);
		}

		$field['notif'] = self::call_fn($_caller, 'is_notif');

 		foreach ($_args as $key => $value)
		{
			switch ($key)
			{
				case 'notif':
				case 'subdomain':
				case 'status':
				case 'code':
				case 'send':
				case 'readdate':
				case 'telegram':
				case 'sms':
				case 'email':
				case 'meta':
				case 'from':
				case 'to':
				case 'sms':
				case 'telegram':
				case 'email':
					$field[$key] = $value;
					break;

				default:
					$data[$key] = $value;
					break;
			}
		}

		$new_args         = $field;
		if(!empty($data))
		{
			$new_args['data'] = $data;
		}


		return self::db($_caller, $new_args);
	}


	// save log in database
	public static function db($_caller, $_args = [])
	{
		$default_args =
		[
			'from'      => null,
			'to'      => null,
			'subdomain' => null,
			'data'      => null,
			'status'    => 'enable',
			'code'      => null,
			'send'      => null,
			'notif'     => null,
			'from'      => null,
			'readdate'  => null,
			'meta'      => null,
			'sms'       => null,
			'telegram'  => null,
			'email'     => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		if($_args['from'] && is_numeric($_args['from']))
		{
			$from = $_args['from'];
		}
		else
		{
			$from = \dash\user::id();
		}

		if(!$from)
		{
			$from = null;
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
			'caller'       => $_caller,
			'from'         => $from,
			'to'           => $_args['to'],
			'datecreated'  => date("Y-m-d H:i:s"),
			'subdomain'    => $_args['subdomain'],
			'visitor_id'   => \dash\utility\visitor::id(),
			'data'         => $_args['data'],
			'status'       => $_args['status'],
			'code'         => $_args['code'],
			'send'         => $_args['send'],
			'notif'        => $_args['notif'],
			'readdate'     => $_args['readdate'],
			'meta'         => $_args['meta'],
			'ip'           => \dash\server::ip(true),
			'sms'          => $_args['sms'],
			'telegram'     => $_args['telegram'],
			'email'        => $_args['email'],
		];

		$log_id = \dash\db\logs::insert($insert_log);
		return $log_id;
	}
}
?>