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

		$replace = [];
		if(isset($_data['data']) && is_string($_data['data']))
		{
			$replace = json_decode($_data['data'], true);
		}

		if(isset($_data['caller']))
		{
			$_data = array_merge($_data, self::caller_detail($_data['caller']));
		}

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
					$result[$key] = T_($value, $replace);
					break;

				case 'send_msg':
					if(is_array($value))
					{
						foreach ($value as $k => $v)
						{
							$result[$key][$k] = T_($v, $replace);
						}
					}
					break;

				case 'send_to':
				case 'user_id':
					$result[$key] = $value;
					if(\dash\temp::get('logLoadUserDetail'))
					{
						$result['user_detail'] = self::detect_user($_data, $key);
					}
					break;


				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;

	}

	private static function detect_user($_detail, $_key)
	{
		$all_user_detail = [];
		if($_key === 'user_id')
		{
			if(isset($_detail['user_id']) && is_numeric($_detail['user_id']))
			{
				$all_user_detail[] = \dash\db\users::get_by_id($_detail['user_id']);
			}
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