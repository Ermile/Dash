<?php
namespace dash\app;


class log
{
	public static $sort_field =
	[
		'id',

	];


	/**
	 * Gets the course.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The course.
	 */
	public static function list($_string = null, $_args = [])
	{

		$default_meta =
		[
			'sort'  => null,
			'order' => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_meta, $_args);

		if($_args['sort'] && !in_array($_args['sort'], self::$sort_field))
		{
			$_args['sort'] = null;
		}


		$result            = \dash\db\logs::search($_string, $_args);
		$temp              = [];

		foreach ($result as $key => $value)
		{
			$check = self::ready($value);
			if($check)
			{
				$temp[] = $check;
			}
		}

		return $temp;
	}

	private static $caller = [];


	private static function caller_detail($_caller)
	{
		if(empty(self::$caller))
		{
			self::$caller = \dash\log::lists();
		}


		if(array_key_exists($_caller, self::$caller))
		{
			return self::$caller[$_caller];
		}
		return [];
	}


	public static function ready($_data)
	{
		if(!is_array($_data))
		{
			return false;
		}

		$result = [];

		if(isset($_data['caller']))
		{
			$_data = array_merge($_data, self::caller_detail($_data['caller']));
		}

		$replace = [];
		if(isset($_data['data']) && is_string($_data['data']))
		{
			$replace = json_decode($_data['data'], true);
		}

		if(isset($_data['T_']) && is_array($_data['T_']))
		{
			$_data['T_'] = array_map('T_', $_data['T_']);
			$replace = array_merge($replace, $_data['T_']);
		}

		$user_detail = self::detect_user($_data, 'user_id');

		foreach ($_data as $key => $value)
		{
			switch ($key)
			{
				case 'id':
					$result[$key]     = $value;
					$result['id_raw'] = $value;
					break;

				case 'caller':
					$result[$key] = $value;
					// $result = array_merge($result, self::caller_detail($value));
					break;

				case 'data':
					if($value && is_string($value))
					{
						$result['data'] = json_decode($value, true);
					}
					break;

				case 'title':
				case 'content':
					// $value = self::userT_($value, $user_detail);
					// $value = self::logT_($value, $_data);
					$result[$key] = T_($value, $replace);
					break;

				case 'send_msg':
					if(is_array($value))
					{
						foreach ($value as $k => $v)
						{
							// $v = self::userT_($v, $user_detail);
							// $v = self::logT_($v, $_data);
							$result[$key][$k] = T_($v, $replace);
						}
					}
					break;

				case 'send_to':
					$result[$key] = $value;
					if(\dash\temp::get('logLoadUserDetail'))
					{
						$result['user_detail'] = self::detect_user($_data, $key);
					}
					break;

				case 'datecreated':
					$result[$key]              = $value;
					$result['longdatecreated'] = \dash\datetime::fit($value, true);
					break;

				case 'user_id':
					$result[$key]        = $value;
					$result['user_code'] = \dash\coding::encode($value);
					break;

			default:
				$result[$key] = $value;
				break;
			}
		}


		foreach ($result as $key => $value)
		{
			switch ($key)
			{
				case 'title':
				case 'content':
					$value = self::userT_($value, $user_detail);
					$value = self::logT_($value, $result);
					$result[$key] = $value;
					break;


				case 'send_msg':
					if(is_array($value))
					{
						foreach ($value as $k => $v)
						{
							$v                = self::userT_($v, $user_detail);
							$v                = self::logT_($v, $result);
							$result[$key][$k] = $v;
						}
					}
					break;


			default:
				$result[$key] = $value;
				break;
			}
		}

		return $result;

	}

	public static function myT_($_data, $_replace)
	{
		if(is_array($_data))
		{
			foreach ($_data as $key => $value)
			{
				$_data[$key] = self::myT_($value, $_replace);
			}
		}
		else
		{
			$_data = self::logT_($_data, $_replace);
		}
		return $_data;
	}


	public static function logT_($_string, $_replace)
	{

		if(strpos($_string, '|') !== false)
		{
			if(is_array($_replace))
			{
				foreach ($_replace as $key => $value)
				{
					if(is_string($value))
					{
						$_string = str_replace('|'. $key, $value, $_string);
					}
				}
			}
		}
		return $_string;
	}

	public static function userT_($_string, $_replace)
	{

		if(strpos($_string, ';') !== false)
		{
			if(is_array($_replace))
			{
				foreach ($_replace as $key => $value)
				{
					$_string = str_replace(';'. $key, $value, $_string);
				}
			}
		}
		return $_string;
	}


	private static function detect_user($_detail, $_key)
	{
		$all_user_detail = [];
		if($_key === 'user_id')
		{
			if(isset($_detail['user_id']) && is_numeric($_detail['user_id']))
			{
				return \dash\db\users::get_by_id($_detail['user_id']);
			}
			return [];
		}
		else
		{

			$send_to = isset($_detail['send_to']) ? $_detail['send_to'] : null;

			if(!$send_to || !is_array($send_to))
			{
				return false;
			}

			$permission_list = [];
			foreach ($send_to as $key => $value)
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
					$temp = \dash\permission::who_have($value);
					unset($temp['admin']);
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

			$all_user_detail = \dash\db\users::get(['permission' => ["IN", "('$permission_list')"], 'status' => 'active']);

		}

		if(empty($all_user_detail))
		{
			return false;
		}

		// to remove duplicate if exist
		$all_user_detail       = array_combine(array_column($all_user_detail, 'id'), $all_user_detail);
		return $all_user_detail;
	}

}
?>