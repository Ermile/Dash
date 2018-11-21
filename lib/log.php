<?php
namespace dash;


class log
{
	private static $temp_log    = [];
	private static $from_detail = [];


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

		$is_notif = self::call_fn($_caller, 'is_notif');

		$field['notif'] = $is_notif;


		if(isset($_args['from']) && is_numeric($_args['from']))
		{
			$field['from'] = $_args['from'];
		}
		else
		{
			$field['from'] = \dash\user::id();
		}

		if(!$field['from'])
		{
			$field['from'] = null;
		}

		self::set_from($field['from']);

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

		if($is_notif)
		{
			return self::notif($_caller, $new_args);
		}
		else
		{
			return self::db($_caller, $new_args);
		}
	}

	private static function set_from($_user_id)
	{
		if($_user_id)
		{
			$detail = [];
			if(intval($_user_id) === intval(\dash\user::id()))
			{
				$detail = \dash\user::detail();
			}
			else
			{
				$detail = \dash\db\users::get_by_id($_user_id);

			}
			self::$from_detail = $detail;
			return true;
		}
	}

	public static function from_id()
	{
		return self::from_detail('id');
	}

	public static function from_name()
	{
		return self::from_detail('displayname');
	}

	public static function from_detail($_key = null)
	{
		if($_key)
		{
			if(array_key_exists($_key, self::$from_detail))
			{
				return self::$from_detail[$_key];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$from_detail;
		}
	}


	public static function call_fn($_class, $_fn, $_args = [], $_args2 = [])
	{
		$folder = null;
		if(strpos($_class, '_') !== false)
		{
			$folder = substr($_class, 0, strpos($_class, '_'));
		}

		if($folder)
		{
			$project_function = ["\\lib\\app\\log\\caller\\$folder\\$_class", $_fn];
			$dash_function    = ["\\dash\\app\\log\\caller\\$folder\\$_class", $_fn];
		}
		else
		{
			$project_function = ["\\lib\\app\\log\\caller\\$_class", $_fn];
			$dash_function    = ["\\dash\\app\\log\\caller\\$_class", $_fn];
		}

		if(is_callable($project_function))
		{
			$namespace       = $project_function[0];
			$function        = $project_function[1];
			$result_function = $namespace::$function($_args, $_args2);
			return $result_function;
		}
		elseif(is_callable($dash_function))
		{
			$namespace       = $dash_function[0];
			$function        = $dash_function[1];
			$result_function = $namespace::$function($_args, $_args2);
			return $result_function;
		}

		return null;
	}


	private static function notif($_caller, &$_args)
	{
		$send_to         = self::call_fn($_caller, 'send_to');
		$send_to_creator = self::call_fn($_caller, 'send_to_creator');

		if(!$send_to)
		{
			if($send_to_creator)
			{
				$_args['to'] = \dash\user::id();
				$send_args   = self::create_text($_caller, $_args, [\dash\user::id() => \dash\user::detail()]);
				return self::db($_caller, $_args, $send_args);
			}

			return self::db($_caller, $_args);
		}
		else
		{
			$user_detail = self::detect_user($send_to, $send_to_creator);
			if($user_detail)
			{
				$send_args = self::create_text($_caller, $_args, $user_detail);
				return self::db($_caller, $_args, $send_args);
			}
			else
			{
				return self::db($_caller, $_args);
			}
		}
	}


	private static function create_text($_caller, &$_args, $_user_detail)
	{
		$new_args = [];
		// set to all user
		foreach ($_user_detail as $key => $value)
		{
			$new_args[$key]['to']       = $key;
		}

		$master_lang = \dash\language::current();

		$telegram      = self::call_fn($_caller, 'telegram');
		if($telegram)
		{
			foreach ($_user_detail as $key => $value)
			{
				if(isset($value['chatid']))
				{
					$current_lang = \dash\language::current();

					if(isset($value['language']) && mb_strlen($value['language']) === 2 && $value['language'] !== $current_lang)
					{
						\dash\language::set_language($value['language']);
					}

					$telegram_text = self::call_fn($_caller, 'telegram_text', $_args, $value['chatid']);
					if($telegram_text)
					{
						$new_args[$key]['telegram'] = addslashes($telegram_text);
					}
				}
			}
		}

		$sms      = self::call_fn($_caller, 'sms');

		if($sms)
		{
			foreach ($_user_detail as $key => $value)
			{
				if(isset($value['mobile']) && \dash\utility\filter::mobile($value['mobile']))
				{
					// check if send by tg not send by sms
					if(isset($new_args[$key]['telegram']))
					{
						if(!self::call_fn($_caller, 'force_send_sms'))
						{
							continue;
						}
					}

					$current_lang = \dash\language::current();

					if(isset($value['language']) && mb_strlen($value['language']) === 2 && $value['language'] !== $current_lang)
					{
						\dash\language::set_language($value['language']);
					}

					$sms_text = self::call_fn($_caller, 'sms_text', $_args, $value['mobile']);
					if($sms_text)
					{
						$new_args[$key]['sms'] = addslashes($sms_text);
					}
				}
			}
		}

		$email      = self::call_fn($_caller, 'email');

		if($email)
		{
			foreach ($_user_detail as $key => $value)
			{
				if(isset($value['email']))
				{
					// check if send by tg not send by sms
					if(isset($new_args[$key]['telegram']) || isset($new_args[$key]['sms']))
					{
						if(!self::call_fn($_caller, 'force_send_email'))
						{
							continue;
						}
					}

					$current_lang = \dash\language::current();

					if(isset($value['language']) && mb_strlen($value['language']) === 2 && $value['language'] !== $current_lang)
					{
						\dash\language::set_language($value['language']);
					}

					$email_text = self::call_fn($_caller, 'email_text', $_args, $value['email']);
					if($email_text)
					{
						$new_args[$key]['email'] = json_encode(['email' => $value['email'], 'text' => $email_text], JSON_UNESCAPED_UNICODE);
					}
				}
			}
		}

		\dash\language::set_language($master_lang);

		return $new_args;
	}






	private static function detect_user($_send_to, $_send_to_creator = false)
	{
		$all_user_detail = [];

		if($_send_to_creator)
		{
			$all_user_detail[] = \dash\user::detail();
		}

		if($_send_to && is_array($_send_to))
		{
			$permission_list = [];
			foreach ($_send_to as $key => $value)
			{
				if($value === 'supervisor')
				{
					$permission_list[] = 'supervisor';
				}
				elseif($value === 'admin')
				{
					$permission_list[] = 'admin';
				}
				else
				{
					$temp   = \dash\permission::who_have($value);
					$temp[] = 'supervisor';
					if(!empty($temp))
					{
						$permission_list = array_merge($permission_list, $temp);
					}
				}
			}

			$permission_list = array_filter($permission_list);
			$permission_list = array_unique($permission_list);

			if(!empty($permission_list))
			{
				$permission_list = implode("','", $permission_list);
			}

			$public_show_field = "users.id, users.mobile, users.chatid, users.displayname, users.email, users.language";

			$temp = \dash\db\users::get(['permission' => ["IN", "('$permission_list')"], 'status' => 'active'], ['public_show_field' => $public_show_field]);
			$all_user_detail = array_merge($all_user_detail, $temp);
		}

		if(empty($all_user_detail))
		{
			return false;
		}

		// to remove duplicate if exist
		$all_user_detail       = array_combine(array_column($all_user_detail, 'id'), $all_user_detail);

		return $all_user_detail;
	}



	// save log in database
	public static function db($_caller, $_args = [], $_multi_send = [])
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
			'from'         => $_args['from'],
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

		if($_multi_send)
		{
			$multi_record = [];
			foreach ($_multi_send as $key => $value)
			{
				$multi_record[] = array_merge($insert_log, $value);
			}

			if(!empty($multi_record))
			{
				$log_id = \dash\db\logs::multi_insert($multi_record);
				return $log_id;
			}
		}
		else
		{
			$log_id = \dash\db\logs::insert($insert_log);
			return $log_id;
		}


	}
}
?>