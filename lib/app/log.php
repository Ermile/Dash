<?php
namespace dash\app;


class log
{
	public static $sort_field =
	[
		'id',
	];

	public static function set_readdate($_data, $_all = false)
	{
		if(!is_array($_data))
		{
			return false;
		}

		if($_all)
		{
			return \dash\db\logs::set_readdate_user(\dash\user::id());
		}
		else
		{

			$ids = array_column($_data, 'id_raw');
			$ids = array_filter($ids);
			$ids = array_unique($ids);

			if(!$ids)
			{
				return false;
			}

			$ids = implode(',', $ids);

			return \dash\db\logs::set_readdate($ids);
		}

	}


	public static function my_notif_count($_user_id = null)
	{
		if(!$_user_id || !is_numeric($_user_id))
		{
			$_user_id = \dash\user::id();
		}

		$count = null;

		if($_user_id)
		{
			$count = \dash\db\logs::my_notif_count($_user_id);
		}
		return intval($count);
	}

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

	public static function ready($_data)
	{
		if(!is_array($_data))
		{
			return false;
		}

		$result = [];

		$caller = null;


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

				case 'caller':
					$result[$key] = $value;
					$caller       = $value;
					break;

				default:
					$result[$key] = $value;
					break;
			}
		}

		if($caller)
		{
			$result_fn = \dash\log::call_fn($caller, 'site', $result);
			if($result_fn && !is_array($result_fn))
			{
				$result['title'] = $result_fn;
			}
			elseif($result_fn && is_array($result_fn))
			{
				$result = array_merge($result, $result_fn);
			}
		}


		return $result;
	}


}
?>