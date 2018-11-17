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


	public static function ready($_data)
	{
		if(!is_array($_data))
		{
			return false;
		}

		$result = [];

		$project_function = ["\\lib\\app\\log\\caller\\$_caller", 'list'];

		$dash_function    = ["\\dash\\app\\log\\caller\\$_caller", 'list'];

		if(is_callable($project_function))
		{
			$namespace       = $project_function[0];
			$function        = $project_function[1];
			$result_function = $namespace::$function($_data);

			if(is_array($result_function))
			{
				$_data = array_merge($_data, $result_function);
			}
		}
		elseif(is_callable($dash_function))
		{
			$namespace       = $dash_function[0];
			$function        = $dash_function[1];
			$result_function = $namespace::$function($_data);

			if(is_array($result_function))
			{
				$_data = array_merge($_data, $result_function);
			}
		}


		foreach ($_data as $key => $value)
		{
			switch ($key)
			{
				case 'id':
					$result[$key]     = $value;
					$result['id_raw'] = $value;
					break;

				case 'data':
					if($value && is_string($value))
					{
						$result['data'] = json_decode($value, true);
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

		return $result;
	}




	private static function detect_user($_detail, $_key = null)
	{
		$all_user_detail = [];
		if($_key === 'force')
		{
			if(isset($_detail['user_id']) && is_numeric($_detail['user_id']))
			{
				return \dash\db\users::get_by_id($_detail['user_id']);
			}
			return [];
		}
		elseif($_key === 'user_id')
		{
			if(isset($_detail['not_send_to_userid']) && $_detail['not_send_to_userid'])
			{
				return [];
			}

			if(isset($_detail['user_id']) && is_numeric($_detail['user_id']))
			{
				return \dash\db\users::get_by_id($_detail['user_id']);
			}
			return [];
		}
		else
		{
			$all_user_detail = [];

			$send_to = isset($_detail['send_to']) ? $_detail['send_to'] : null;

			if($send_to && is_array($send_to))
			{
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

			if(isset($_detail['user_id']) && is_numeric($_detail['user_id']))
			{
				if(isset($_detail['not_send_to_userid']) && $_detail['not_send_to_userid'])
				{
					// not send to user id
				}
				else
				{
					$temp = \dash\db\users::get_by_id($_detail['user_id']);
					if(is_array($temp))
					{
						$all_user_detail[] = $temp;
					}
				}
			}
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